<?php
/**
 * @package    Publisher
 * @subpackage Model
 * @version    $Id: User.php 30226 2011-11-18 18:44:48Z sparsley $
 */

/**
 * Class for representing a row from the login table
 *
 * @package    Publisher
 * @subpackage Model
 */
class User extends BaseObject
{
    //user interface constants
    const USER_INTERFACE_3_5 = 0;
    const USER_INTERFACE_4_0 = 1;
    const USER_INTERFACE_3_5_SWITCHABLE = 2;
    const USER_INTERFACE_4_0_SWITCHABLE = 3;
    const USER_INTERFACE_3_5_TEMPORARY = 4;
    const USER_INTERFACE_4_0_TEMPORARY = 5;
    //password change constants
    const USER_PASSWORD_CHANGE = 3;
    const USER_PASSWORD_CHANGE_BYPASSEMAIL = 7;

    const CacheKey = "user-"; // user cache key

    /**
     * @var array $aFieldNames
     */
    protected $fieldNames = array (
        'clientid'            => 'clientid',
        'userid'              => 'loginid', //alias
        'loginid'             => 'loginid',
        'username'            => 'login',
        'password'            => 'password',
        'firstname'           => 'fname',
        'lastname'            => 'lname',
        'accountowner'        => 'admin',
        'enabled'             => 'enabled',
        'viewlevel'           => 'view_level',
        'editor'              => 'editor',
        'texteditor'          => 'texteditor',
        'userinterface'       => 'user_interface',
        'email'               => 'email', // this is the email address for the user, not the account.
        // this email should be used to contact the user for
        // user-related actions (e.g. having the user change their password)
        // and not for account-related actions.
    );

    protected $aApiFieldNames = array(
        'userid'              => 'userId',
        'username'            => 'userName',
        'password'            => 'password',
        'firstname'           => 'firstName',
        'lastname'            => 'lastName',
        'enabled'             => 'enabled',
        'editor'              => 'editor',
        'email'               => 'email',
    );

    /**
     * @var array loginid is the PrimaryKey for the login table
     */
    protected $aPrimaryKey = array('auto'=>'userid');

    /**
     * @var string login is the table name for Users
     */
    protected $sTableName = 'login';

    /**
     * Constructor returns a User
     * @param array $aValues required=false
     * @return User
     */
    public function __construct($aValues = array())
    {
        if ($aValues instanceof ICPUser) {
            $aICPValues                  = array();
            $aICPValues['userid']        = $aValues->getUserID();
            $aICPValues['clientid']      = $aValues->getClientID();
            $aICPValues['username']      = $aValues->getUsername();
            $aICPValues['password']      = $aValues->getPassword();
            $aICPValues['firstname']     = $aValues->getFirstName();
            $aICPValues['lastname']      = $aValues->getLastName();
            $aICPValues['admin']         = $aValues->getAdmin();
            $aICPValues['enabled']       = $aValues->getEnabled();
            $aICPValues['viewlevel']     = $aValues->getViewLevel();
            $aICPValues['editor']        = $aValues->getHtmlEditor();
            $aICPValues['texteditor']    = $aValues->getTextEditor();
            $aICPValues['userinterface'] = $aValues->getUserInterface();
            $aICPValues['email']         = $aValues->getEmail();
            $aValues = $aICPValues;
        }

        // setPassword MUST be called AFTER setUsername; Move it to the end of aValues.
        if(isset($aValues['password'])) {
            $sPass = $aValues['password'];
            unset($aValues['password']);
            $aValues['password'] = $sPass;
        }
        parent::__construct($aValues);

        // default values copied from ICPUser __construct
        if (!isset($this->aFields['admin'])) {
            $this->aFields['admin'] = 0;
        }
        if (!isset($this->aFields['enabled'])) {
            $this->aFields['enabled'] = 1;
        }
        if (!isset($this->aFields['editor'])) {
            $this->aFields['editor'] = 3;
        }
        if (!isset($this->aFields['texteditor'])) {
            $this->aFields['texteditor'] = 0;
        }
        if (!isset($this->aFields['view_level'])) {
            $this->aFields['view_level'] = 0;
        }
        if (!isset($this->aFields['user_interface'])) {
            $this->aFields['user_interface'] = ICPUser::USER_INTERFACE_4_0;
        }
        if (!isset($this->aFields['email'])) {
            $this->aFields['email'] = null;
        }
    }

    /**
     * Delete the user.
     * @return integer
     */
    public function delete()
    {
        $iAffectedRows = parent::delete();
        CacheService::getInstance()->delete(User::CacheKey . $this->getUserId());
        CacheService::getInstance()->delete("user-{$this->getClientID()}-{$this->getUserID()}-permissions");
        return $iAffectedRows;
    }

    /**
     * Checks to see if a user is an admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return (bool) $this->getAccountOwner();
    }

    /**
     * Overrides existing magic setPassword ability so we can hash the password.
     *
     * @param string password
     */
    public function setPassword($sPassword)
    {
        // Only hash this if the password being passed in is not already hashed.
        if (strlen($sPassword) < 128) {
            $this->aFields['password'] = hashPassword($sPassword, $this->getUsername());
        } else {
            $this->aFields['password'] = $sPassword;
        }
    }

    /**
     * Renames fields with api-specific field names, based on the provided input
     * Removes fields that exist in the object but not in aApiFieldNames
     *
     * @return array $aOutput
     */
    public function toApi()
    {
        $aOutput = parent::toApi();

        // We do not pass back the password to the user
        unset($aOutput['password']);

        return $aOutput;
    }

    /**
     * Update the user.
     */
    public function update()
    {
        parent::update();
        CacheService::getInstance()->delete(User::CacheKey . $this->getUserId());
        CacheService::getInstance()->delete("user-{$this->getClientID()}-{$this->getUserID()}-permissions");
    }

    /////////////////////////////////////////////////////////////////////////////
    // PSUEDO-PROTECTED ////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////
    // PROTECTED //////////////////////////////////////
    //////////////////////////////////////////////////
}

