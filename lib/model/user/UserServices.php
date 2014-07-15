<?php
/**
 * @package    Publisher
 * @subpackage Service
 * @version    $Id: UserService.php 31424 2012-01-20 21:36:28Z gcatlin $
 */

/**
 * Service for handling Users.
 *
 * @package    Publisher
 * @subpackage Service
 */
class UserServices extends BaseObjectServices
{
    const PermissionNone = 0;
    const PermissionHalf = 1;
    const PermissionFull = 3;

    const AdminClientFolderId = 0;

    const PasswordChangeTokenExpires = 259200; // 3 days

    /////////////////////////////////////////////////////////////////////////////
    // STATIC //////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Retrieves an instance of the Service.
     *
     * @return UserService
     */
    public static function getInstance()
    {
        return Service::_getService('User');
    }

    /////////////////////////////////////////////////////////////////////////////
    // PUBLIC //////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Change a user's password in the publisher.
     *
     * @param integer $iClientId   required=true
     * @param integer $iLoginId    required=true
     * @param string  $sPassword   required=true
     * @param string  $sToken      required=false
     * @return boolean
     */
    public function changePassword($iClientId, $iLoginId, $sPassword, $sToken)
    {
        $bStatus = $this->_changePassword($iClientId, $iLoginId, $sPassword);

        if (!$bStatus) {
            return false;
        }

        // remove the reset password token
        if ($sToken) {
            $sSql = "DELETE FROM forgot_password_link WHERE client_id=? AND ref_string=?";
            D::$db_admin->query($sSql, array($iClientId, $sToken));
            $sSql = "UPDATE login SET user_interface=1 WHERE loginid=?";
            D::$db_admin->query($sSql, array($iLoginId));
            CacheService::getInstance()->delete(User::CacheKey . $iLoginId);
        }

        return true;
    }

    /**
     * Update a user's username in the publisher
     *
     * @param integer $iClientid required=true
     * @param $sOldUsername required=true
     * @param $sNewUsername required=true
     * @return boolean True if the username was successfully updated, False otherwise
     *
     */
    public function changeUsername($iClientId, $sOldUsername, $sNewUsername)
    {
        // Do necessary validation on input

        // Does the provided Client actually own the username in question?
        if (!($iClientId == $this->getClientByLogin($sOldUsername))){
            return "ERROR: The specified login does not belong to the specified client.";
        }

        // Has this usernmame already been changed?
        if ($this->previouslyChangedUsername($this->getUserIdByLogin($sOldUsername))){
            return "ERROR: This username has been changed previously.";
        }

        // Is the requested username available?
        if (!$this->isUsernameAvailable($sNewUsername)){
            return "ERROR: The requested username is already in use.";
        }

        $iUser = $this->getUserIdByLogin($sOldUsername);
        $oUser = @ICPUser::loadByID($iUser);

        // Call protected function to make the update
        $bChangedUsername = $this->_changeUsername($oUser, $sOldUsername, $sNewUsername);

        if(!$bChangedUsername){
            return false;
        }

        // At this point, the username change was successful.
        // Now, let's insert the accountvalue indicating so.
        $oAccountValue = new ICPAccountValue();
        $oAccountValue->setClientId($iClientId);
        $oAccountValue->setName("_changelogin_" . $this->getUserIdByLogin($sNewUsername));
        $oAccountValue->setValue(date('Y-m-d H:i:s'));
        $oAccountValue->create();

        return true;
    }

    /**
     * Deletes a User from the database
     *
     * @param integer $iClientId  required=true
     * @param integer $iUserId    required=true
     * @return boolean
     */
    public function deleteUser($iClientId, $iUserId)
    {
        $oUser = $this->getUser($iUserId);

        if($oUser->getClientId() != $iClientId) {
            throw new ServiceException("You may not delete a user from another account.", Service::NoResult);
        }

        if($oUser->getAccountOwner()) {
            throw new ServiceException("You may not delete the account owner.", Service::Unauthorized);
        }

        return $oUser->delete() == 1;
    }

    /**
     * Deletes all the password reset tokens for a user.
     *
     * @param integer $iClientId  required=true
     * @param integer $iUserId    required=true
     * @return integer Returns the number of tokens deleted
     */
    public function deletePasswordResetTokens($iClientId, $iUserId)
    {
        return Peer::getPeer('User')->deletePasswordResetTokens($iClientId, $iUserId);
    }

    /**
     * Clears the promotion dismissal flag so the user can see the promotion again
     *
     * @param  integer $iClientId        required=true
     * @param  integer $iUserId          required=true
     * @param  boolean $bDisplay         required=true
     * @return null
     **/
    public function displayPromotion($iClientId, $iUserId, $bDisplay)
    {
        $oAccountManager = new ICPAccountManager();
        $oAccountManager->setAccountValue($iClientId, "dismissPromotion_{$iUserId}", !$bDisplay);
    }

    /**
     * Retrieves the ID of the admin user for the specified account.
     *
     * @param integer $iClientId
     * @return integer
     */
    public function getAccountAdminUserId($iClientId)
    {
        return Peer::getPeer('User')->getAccountAdminUserId($iClientId);
    }

    /**
     * Retrieves the username of the admin user for the specified account.
     *
     * @param integer $iClientId
     * @return string
     */
    public function getAccountAdminUserName($iClientId)
    {
        return Peer::getPeer('User')->getAccountAdminUserName($iClientId);
    }

    /**
     * Get raw text for e-mail to get a user to change their password.
     * @param integer $iUserId required=true
     * @param boolean $bTextOnly required=false preset=false
     * @return string
     */
    public function getChangePasswordEmailText($iUserId, $bTextOnly=false)
    {
        if (!Config::get('security.pwupdate')) {
            throw new ServiceException('Changing passwords is not enabled');
        }
        $oUser = $this->getUser($iUserId);
        $iClientId = $oUser->getClientID();
        $oAccount = Service::_getService('Account')->getAccount($iClientId);

        if ($oAccount->getMultiLogin()) {
            if ($oUser->isAdmin()) {
                $sTemplateDir = 'passwordnotifyadmin';
            } else {
                return false; // non-admin multi-profile users shouldn't get e-mails
            }
        } else {
            $sTemplateDir = 'passwordchangenotify';
        }

        $sEmailAddress = $oAccount->getEmail();
        $sLogin = $oUser->getUsername();

        $sToken = $this->getPasswordResetToken($iClientId, $iUserId);
        $sUrl = ICPCoreURL . '/password/change?sToken='.$sToken;
        $sSupportUrl = ICPUrl . '/support';
        $sPhone = '(877) 968-3996';
        $sLocalPhone = '(919) 968-3996';

        $aData = $this->createChangePasswordEmail($sTemplateDir, $sEmailAddress, $sLogin, $sUrl, $sPhone, $sLocalPhone, $sSupportUrl);

        if ($bTextOnly) {
            return $aData['sTextBody'];
        } else {
            $aData['sUserEmail'] = $sEmailAddress;
            return $aData;
        }
    }


    /**
     * Will return the ID of the user who is currently logged in.
     * If the user is an admin, you will get their id, not who
     * they are logged in as.
     *
     * @return integer Id of user who is currently logged in.
     */
    public function getCurrentlyLoggedInUser()
    {
        $oUser = $this->getCurrentlyLoggedInUserObject();
        return ($oUser instanceof ICPUser) ? $oUser->getUserId() : null;
    }

    /**
     * Will return the user who is currently logged in.
     * If the user is an admin, you will get that user, not who
     * they are logged in as.
     *
     * @return ICPUser
     */
    public function getCurrentlyLoggedInUserObject()
    {
        $oManager = ICPApplicationManager::getSessionManager();
        $iAdminId = @$oManager->get('iAdminLoginId');
        $iPartnerAdminId = @$oManager->get('iPartnerAdminLoginId');
        if (is_null($iAdminId) && is_null($iPartnerAdminId)) {
            //not an admin logged in, return normal user
            $oUser = $oManager->getUser();
            if (! is_object($oUser)) {
                return null; // if there is no logged in user return null instead of throwing a fatal error.
            }
        } else {
            $oUser = new ICPUser();
            if(!is_null($iAdminId)) {
                $oUser = $oUser->loadById($iAdminId);
            } else {
                $oUser = $oUser->loadById($iPartnerAdminId);
            }
        }

        return $oUser;
    }

    /**
     * Will return the id of the admin who is currently logged in
     * Returns false if not logged in as admin
     *
     * @return integer Id of the admin who is currently logged in, or
     * 	false if no admin is logged in
     */
    public function getAdminUserId()
    {
        $oManager = ICPApplicationManager::getSessionManager();
        $iAdminId = @$oManager->get('iAdminLoginId');

        if ($iAdminId === null) {
            //not an admin logged in, return false
            return false;
        }

        return $iAdminId;

    }

    /**
     * Get the email address associated with a username.
     *
     * @param string $sUsername  required=true
     * @return string
     */
    public function getEmailForUsername($sUsername)
    {
        // get the email address associated with the account
        $sRecipient = D::$db_admin->getOne("SELECT account.email FROM account JOIN login USING(clientid) WHERE login.login=?", array($sUsername));

        if (!$sRecipient) {
            return null;
        }

        return $sRecipient;
    }

    /**
     * Get the id associated with a username.
     *
     * @param string $sUsername  required=true
     * @return string
     */
    public function getUseridForUsername($sUsername)
    {
        // get the email address associated with the account
        $iUserId = D::$db_admin->getOne("SELECT loginid FROM login WHERE login=?", array($sUsername));

        if (!$iUserId) {
            return null;
        }

        return $iUserId;
    }

    /**
     * Returns the content of the un-dismissed promotion for a given user
     *
     * @param  integer $iClientId        required=true
     * @param  integer $iUserId          required=true
     * @return string
     **/
    public function getPromotion($iClientId, $iUserId)
    {
        if($this->userHasDismissedPromotion($iClientId, $iUserId)) {
            return false;
        }
        $oPromotion = new Promotion();
        $oAccount = Service::_getService('Account')->getAccount($iClientId);
        if($oAccount->getDemo() == 1) {
            return $oPromotion->getContentForTrial($iClientId);
        } else {
            return $oPromotion->getContent($iClientId);
        }
    }

    /**
     * Returns an existing password reset token or generates a new token.
     *
     * @param integer $iClientId  required=true
     * @param integer $iUserId    required=true
     * @return string
     */
    public function getPasswordResetToken($iClientId, $iUserId)
    {
        $sToken = Peer::getPeer('User')->getPasswordResetToken($iUserId);
        if ($sToken === null) {
            // generate the password reset token
            $sToken = md5(uniqid(rand(), true) . $iUserId);
            Peer::getPeer('User')->setPasswordResetToken($iClientId, $iUserId, $sToken);
        }
        Peer::getPeer('User')->deletePasswordResetTokens($iClientId, $iUserId, $sToken);
        return $sToken;
    }

    /**
     * Returns the lib/classes ICPSession object.
     *
     * @param string $sSessionId  required=true
     * @return ICPSession
     */
    public function getICPSession($sSessionId)
    {
        $oIcpSession = false;
        if (Config::get('caching.icpsessionobject')) {
            $sCacheKey = "icpsession-{$sSessionId}";
            $oIcpSession = CacheService::getInstance()->get($sCacheKey);
        }

        if ($oIcpSession === false) {
            $oIcpSession = new ICPSession();
            $oIcpSession->setSessionId($sSessionId);
            $oIcpSession->load();

            if (Config::get('caching.icpsessionobject') &&
                $oIcpSession->getSessionId() != null) {
                // @TODO session stuff should not use cacheservice
                CacheService::getInstance()->set($sCacheKey, $oIcpSession, 86400); // 24 hours
            }
        }

        return $oIcpSession;
    }

    /**
     * Returns a userid for a password reset token.
     *
     * @param string $sToken  required=true
     * @return integer Returns a userid or null for an invalid token
     */
    public function getUserIdFromPasswordResetToken($sToken)
    {
        return Peer::getPeer('User')->getUserIdFromPasswordResetToken($sToken);
    }

    /**
     * Loads the ICPUser object for a UserId.
     *
     * @param integer $iUserId  required=true
     * @return ICPUser
     */
    public function getICPUser($iUserId)
    {
        $oIcpUser = false;
        if (Config::get('caching.userobject')) {
            $sCacheKey = User::CacheKey . $iUserId;
            $aIcpUser = CacheService::getInstance()->get($sCacheKey);
            if (is_array($aIcpUser)) {
                $oIcpUser = new ICPUser();
                $oIcpUser->loadFromArrayWithCompatibleNames($aIcpUser);
            }
        }

        if ($oIcpUser === false) {
            $oIcpUser = new ICPUser();
            $oIcpUser->setUserId($iUserId);
            $oIcpUser->load();

            if (Config::get('caching.userobject') && $oIcpUser->getUserId() != null) {
                CacheService::getInstance()->set($sCacheKey, $oIcpUser->toArrayWithCompatibleNames(), 600); // 10 minutes
            }
        }

        return $oIcpUser;
    }

    /**
     * Loads the model User object for a UserId.
     *
     * @param integer $iUserId  required=true
     * @return User
     */
    public function getUser($iUserId)
    {
        $oUser = false;
        if (Config::get('caching.userobject')) {
            $sCacheKey = User::CacheKey . $iUserId;
            $aUser = CacheService::getInstance()->get($sCacheKey);
            if (is_array($aUser)) {
                $oUser = new User($aUser);
            }
        }

        if ($oUser === false) {
            $oUser = new User();
            $oUser->setUserId($iUserId);
            $oUser->load();

            if (Config::get('caching.userobject') && $oUser->getUserId() != null) {
                CacheService::getInstance()->set($sCacheKey, $oUser->toArray(), 600); // 10 minutes
            }
        }

        return $oUser;
    }

    /**
     * Returns a UserId for the specified username. This method is an alias
     * to getUserIdForUsername().
     *
     * @param string $sLogin
     * @return integer
     * @deprecated Use getUserIdForUsername() instead
     * @todo Find references to this method, convert to use the aliased one instead.
     * @todo Delete this method once aliased method is used instead.
     */
    public function getUserIdByLogin($sLogin)
    {
        return $this->getUserIdForUsername($sLogin);
    }

    /**
     *  Return clientid for login
     *
     * @param string $sLogin
     * @return integer
     */
    public function getClientByLogin($sLogin)
    {
        $iClientId = D::$db_admin->getOne("SELECT clientid FROM login WHERE login=?", array($sLogin));
        return $iClientId;
    }

    /**
     * Checks the the level of the specified permission for the specified user on
     * the specified client folder.
     *
     * @param integer $iUserId          required=true
     * @param integer $iClientFolderId  required=true
     * @param string  $sPermission      required=true
     * @return integer The level the user has for the specified permission
     */
    public function getUserPermissionLevel($iUserId, $iClientFolderId, $sPermission)
    {
        $oUser = $this->getICPUser($iUserId);
        $aPermissions = @ICPPermissionManager::getPermissionsForUser($oUser);

        $iLevel = isset($aPermissions[$iClientFolderId][$sPermission]) ? $aPermissions[$iClientFolderId][$sPermission] : PermissionService::PermissionLevelNone;

        return $iLevel;
    }

    /**
     * Returns a list of users associated with a client id
     * @param integer $iClientId required=true
     * @param integer $iLimit    required=false
     * @param integer $iOffset   required=false
     * @return Collection $oUsers
     */
    public function getUsersByClientId($iClientId, $iLimit = null, $iOffset = null)
    {
        return Peer::getPeer('User')->getUsersByClientId($iClientId, $iLimit, $iOffset);
    }

    /**
     * Returns a user with the specified email address
     *
     * @param string $email required=true
     * @return User
     */
    public function getUserByEmail($email)
    {
        return Peer::getPeer('User')->getUserByEmail($email);
    }

    /**
     * Returns whether the user is the account owner of a partner account
     *
     * @param integer $iUserId
     * @return boolean $bPartnerAdmin
     */
    public function isPartnerAdmin($iUserId)
    {
        $oUser = $this->getUser($iUserId);
        $iUserClientId = $oUser->getClientId();
        $oUserAccount = Service::_getService('Account')->getAccount($iUserClientId);
        $bPartnerAccount = $oUserAccount->getFlist() == Account::Partner;
        $bAccountOwner = $oUser->isAdmin();
        $bPartnerAdmin = $bPartnerAccount && $bAccountOwner;
        return $bPartnerAdmin;
    }

    /**
     * Checks whether a given username is available.
     *
     * @param string $sUsername  required=true
     * @return boolean true if username is available, false otherwise
     */
    public function isUsernameAvailable($sUsername)
    {
        return Peer::getPeer('User')->isUsernameAvailable($sUsername);
    }

    /**
     * sends a change password success email
     *
     * @param  integer $iUserId          required=true
     * @todo email header information should come from the smarty template
     **/
    public function sendChangePasswordSuccessEmail($iUserId)
    {
        if (!Config::get('security.pwupdate')) {
            throw new ServiceException('Changing passwords is not enabled');
        }

        $sFromEmail = 'noreply@icontact.com';
        $sFromName = 'iContact Support';
        $sSubject = 'Password Successfully Updated';

        $oUser = $this->getUser($iUserId);
        $iClientId = $oUser->getClientId();
        $oAccount = Service::_getService('Account')->getAccount($iClientId);

        $sToEmail = $oAccount->getEmail();
        $sLogin = $oUser->getUsername();

        $aBodies = $this->createChangePasswordSuccessEmail($sToEmail, $sLogin);
        $sTextBody = $aBodies['sTextBody'];
        $sHtmlBody = $aBodies['sHtmlBody'];

        Service::_getService('Message')->sendEmail($sFromEmail, $sFromName, $sToEmail, $sSubject, $sTextBody, $sHtmlBody);
    }

    /**
     * Send out the e-mail to get a user to change their password
     * @param integer $iUserId required=true
     * @return boolean
     */
    public function sendChangePasswordEmail($iUserId)
    {
        if (!Config::get('security.pwupdate')) {
            throw new ServiceException('Changing passwords is not enabled');
        }
        $bSuccess = false;
        $aData = $this->getChangePasswordEmailText($iUserId, false);

        if ($aData !== false) { // e-mail should go out
            $sEmailAddress = $aData['sUserEmail'];
            $sTextBody = $aData['sTextBody'];
            $sHtmlBody = $aData['sHtmlBody'];

            $sFromEmail = 'noreply@icontact.com';
            $sFromName = 'iContact Support';
            $sSubject = 'iContact Password Update Request';
            $bSuccess = Service::_getService('Message')->sendEmail($sFromEmail, $sFromName, $sEmailAddress, $sSubject, $sTextBody, $sHtmlBody);
        }

        return $bSuccess;
    }


    /**
     * Updates the login database table to store whether a user needs to change their password
     * @param $iUserId   required=true
     * @param $iSetting  required=true
     */
    public function updatePasswordChangeSetting($iUserId, $iSetting)
    {
        if (!Config::get('security.pwupdate')) {
            throw new ServiceException('Changing passwords is not enabled');
        }
        $aValidSettings = array(User::USER_INTERFACE_4_0, User::USER_PASSWORD_CHANGE, User::USER_PASSWORD_CHANGE_BYPASSEMAIL);
        if (!in_array($iSetting, $aValidSettings)) {
            throw new ServiceException('Invalid setting provided');
        }

        $oUser = new User();
        $oUser->setUserId($iUserId);
        $oUser->load();
        $oUser->setUserInterface($iSetting);
        $oUser->update();
    }

    /**
     * Indicates whether user has dismissed promotion
     *
     * @param  integer $iClientId        required=true
     * @param  integer $iUserId          required=true
     * @return boolean
     **/
    public function userHasDismissedPromotion($iClientId, $iUserId)
    {
        $oAccountManager = new ICPAccountManager();
        return ($oAccountManager->getAccountValue($iClientId, "dismissPromotion_{$iUserId}") == 1);
    }

    /**
     * Determines if the specified user has the specified permission at the
     * specified (or greater) level for the specified client folder.
     *
     * @param integer $iUserId          required=true
     * @param integer $iClientFolderId  required=true
     * @param string  $sPermission      required=true
     * @param integer $iLevel           required=true
     * @return boolean
     */
    public function userHasPermissionLevel($iUserId, $iClientFolderId, $sPermission, $iLevel)
    {
        return ($this->getUserPermissionLevel($iUserId, $iClientFolderId, $sPermission) >= $iLevel);
    }

    ////////////////////////////////////////////////////////////////////////////
    // PSEUDO-PROTECTED ///////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    /**
     *
     * @param integer $iClientId  required=true
     * @param integer $iLoginId   required=true
     * @param string  $sPassword  required=true
     * @return boolean
     */
    protected function _changePassword($iClientId, $iLoginId, $sPassword)
    {
        $aUserArray = array();

        $oUser = new ICPUser();
        $oUser->setClientID($iClientId);
        $oUser->setUserID($iLoginId);
        $oUser->load();

        $aUserArray = array(
            'sLogin'       => $oUser->getUsername(),
            'sPassword'    => $sPassword
        );

        $oUser->setPassword($sPassword);
        $oUser->update();

        return true;
    }

    /**
     * Helper method for changing usernames
     * Validation was done in the base method
     * This does the actual db updates
     *
     * @param object $oUser      required = true
     * @param string $sOld       required = true
     * @param string $sNew       required = true
     * @return boolean
     */
    protected function _changeUsername($oUser, $sOld, $sNew)
    {
        // Let's build the $aUserArray array to pass to ICPUserManager->updateLogin()
        $aUserArray = array(
            'iUserId'     => $oUser->getUserID(),
            'sLogin'      => $sNew,
            'sPassword'   => $oUser->getPassword(),
            'sEmail'      => $this->getEmailForUsername($sOld),
            'sFirst'      => $oUser->getFirstName(),
            'sLast'       => $oUser->getLastName(),
        );

        // Pull current permission info for user
        $oPermissionMgr = new ICPPermissionManager();
        $aPerms = $oPermissionMgr->getPermissionsForUser($oUser);

        //Create our UserManager to do the update for us
        $oUserMgr = new ICPUserManager();
        $iListId=0;
        $iClientId = $oUser->getAccountID();
        $bAllowUsernameChange = true;
        $iSuccess = $oUserMgr->updateLogin($aUserArray, $aPerms, $iClientId, $iListId, $bAllowUsernameChange);

        if ($iSuccess !== $oUser->getUserID()) {   // Upon successful completion, $iSuccess will be equal to the login id
            return false;
        }
        return true;

    }

    ////////////////////////////////////////////////////////////////////////////
    // PROTECTED //////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////

    /**
     * sends a change password email to hand the user their key
     *
     * @param  string $sTemplateDir required=true
     * @param  string $sEmail       required=true
     * @param  string $sLogin       required=true
     * @param  string $sUrl         required=true
     * @param  string $sPhone       required=true
     * @param  string $sSupportUrl  required=true
     * @return array
     **/
    protected function createChangePasswordEmail($sTemplateDir, $sEmail, $sLogin, $sUrl, $sPhone, $sLocalPhone, $sSupportUrl)
    {
        if (!Config::get('security.pwupdate')) {
            throw new ServiceException('Changing passwords is not enabled');
        }

        $aAssigns = array();
        $aAssigns['sEmail']      = $sEmail;
        $aAssigns['sUrl']        = $sUrl;
        $aAssigns['sLogin']      = $sLogin;
        $aAssigns['sPhone']      = $sPhone;
        $aAssigns['sLocalPhone'] = $sLocalPhone;
        $aAssigns['sSupportUrl'] = $sSupportUrl;

        $aSmartyMessage = $this->getSmartyMessageForEmail($sTemplateDir, $aAssigns);

        return array(
            'sTextBody' => $aSmartyMessage['text_body'],
            'sHtmlBody' => $aSmartyMessage['html_body'],
        );
    }

    /**
     * sends a change password success email
     *
     * @param  string $sEmail                 required=true
     * @param  string $sLogin                 required=true
     * @param  string $sPhone                 required=true
     * @param  string $sLocalPhone            required=true
     * @param  string $sSupportTeam           required=true
     * @return array
     **/
    protected function createChangePasswordSuccessEmail($sEmail, $sLogin)
    {
        $aAssigns = array();
        $aAssigns['sEmail'] = $sEmail;
        $aAssigns['sLogin'] = $sLogin;

        $aSmartyMessage = $this->getSmartyMessageForEmail('passwordchangesuccess', $aAssigns);

        return array(
            'sTextBody' => $aSmartyMessage['text_body'],
            'sHtmlBody' => $aSmartyMessage['html_body'],
        );
    }

    /**
     * gets the appropriate smarty data for an email
     *
     * @param string $sTemplateName       required=true
     * @param array  $aAssigns            required=true
     * @return array
     **/
    protected function getSmartyMessageForEmail($sTemplateName, $aAssigns)
    {
        $oMessageManager = new ICPMessageManager();
        return $oMessageManager->loadSmartyMessage($sTemplateName, $aAssigns);
    }

    /**
     * Checks to see if the login has previously been changed
     *
     * @param Login $oLogin
     * @return boolean true if the accountvalue indicating a previous username change exists, false otherwise
     */
    protected function previouslyChangedUsername($iUserId)
    {
        $oUser=$this->getUser($iUserId);
        $sSql = 'SELECT COUNT(*) FROM accountvalue WHERE clientid=? and name=?';     // Does the accountvalue already exist?
        $iChanged = D::$db_admin->getOne( $sSql,array($oUser->getClientID(),'_changelogin_' . $oUser->getUserID()));
        if ($iChanged < 1)
        {
            return false;
        }
        return true;
    }
}
