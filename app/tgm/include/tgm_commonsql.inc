<?php
//=========================================================================================================
//
// DATABASE Connections
//
//=========================================================================================================
function getDatabaseConnection()
  {
    $dbhost     = "localhost";

    $dbname     = "tgm";
    $dbuser     = "tjwolf";
    $dbpassword = "dragon";


 // if (rand(1,2) == 2)
 //    return null;

    $db = mysql_pconnect($dbhost, $dbuser, $dbpassword);
    mysql_select_db($dbname,$db);
    return($db);
  }


//=========================================================================================================
//
// REDIRECT - ensures you pop out of Frames
//
//=========================================================================================================
function topLevelRedirect($url)
  {
    ?>
      <script type="text/javascript">
      <!---
        top.location.href="<?php echo $url?>";
      //-->
      </script>

    <?php
    exit;
  }



//=========================================================================================================
//
// SESSIONS
//
//=========================================================================================================
function newSession($db, $mid, $isAdmin=FALSE)
  {
    global $MAX_SESSION_TIME;

    $mid=strtolower($mid);

    $sql  = "SELECT * FROM member";
    $sql .= " WHERE member_id='$mid'";
    $memresult = exec_query($sql,$db);

    if (($memresult) && ($memrow = mysql_fetch_array($memresult, MYSQL_ASSOC)))
      {
        $is_admin = 0;
        if ($isAdmin)
          $is_admin = 1;

        $MAX_ATTEMPTS=3;
        for ($i=1; $i<=$MAX_ATTEMPTS; $i++)
          {
            $tm=time();
            $expires=$tm+$MAX_SESSION_TIME;
            $sid=generateSession();

            $sql  = "INSERT into session set";
            $sql .= " session_id='$sid',";
            $sql .= " member_id='$mid',";
            $sql .= " expires='$expires',";
            $sql .= " is_admin='$is_admin'";
            $result = exec_query($sql,$db);

            if (($result) && (mysql_affected_rows()==1))
              {
                if (!$isAdmin)
                  {
                    $sql  = "UPDATE member set";
                    $sql .= " lastaccess='$tm', ";
                    $sql .= " date_lastaccess='".formatDate($tm)."' ";
                    $sql .= " WHERE member_id='$mid'";
                    exec_query($sql,$db);
                  }

                $expired=$tm."ZZZZ";
                // DELETE ANY EXPIRED SESSIONS
                $sql  = "DELETE FROM session";
                $sql .= " WHERE expires < '$expired'";
                $result = exec_query($sql,$db);

                return array(0,$sid);
              }
          }
      }
    return (array(-1,0));
  }


function closeSession($db, $sid)
  {
    $sql  = "SELECT * FROM session";
    $sql .= " WHERE session_id='$sid'";
    $result = exec_query($sql,$db);
    if (($result) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        $sql  = "DELETE FROM session";
        $sql .= " WHERE session_id='$sid'";
        $result = exec_query($sql,$db);

        topLevelRedirect(DOMAIN);
        exit;
      }
  }


function updateSession($db, $sid, $mid)
  {
    global $MAX_SESSION_TIME;
    global $RENEW_SESSION_INCREMENT;

          // -- IS Member a Valid/Existing One ??
    $sql  = "SELECT member_id FROM member";
    $sql .= " WHERE member_id='$mid'";
    $memresult = exec_query($sql,$db);
    if (($memresult) && ($memberRecord = mysql_fetch_array($memresult, MYSQL_ASSOC)))
      {
        $sql  = "SELECT expires,is_admin FROM session";
        $sql .= " WHERE session_id='$sid'";
        $sql .= " AND   member_id ='$mid'";
        $result = exec_query($sql,$db);
        if (($result) && ($temp = mysql_fetch_array($result, MYSQL_ASSOC)))
          {
            $tm=time();
            $expires=$temp["expires"];
            $isAdminSession=FALSE;
            if ($temp["is_admin"] != 0)
               $isAdminSession=TRUE;
            // printf("EXPIRES : %d<br>\n",$expires);
            // printf("RENEWX : %d<br>\n",($tm+$RENEW_SESSION_INCREMENT));

            if ($expires < ($tm+$RENEW_SESSION_INCREMENT))
              {
                $sql  = "UPDATE session set";
                $sql .= " expires=".($tm+$RENEW_SESSION_INCREMENT).", ";
                $sql .= " WHERE session_id='$sid'";
                $sql .= " AND   member_id='$mid'";
                $result = exec_query($sql,$db);
              }
            return array(0, $isAdminSession);
          }
      }
  }


function extendSession($db, $sid, $mid, $minutes=60)
  {
    $time_extension = $minutes * 60; // in seconds;

    $sql  = "SELECT expires,is_admin FROM session";
    $sql .= " WHERE session_id='$sid'";
    $sql .= " AND   member_id='$mid'";
    $result = exec_query($sql,$db);
    if (($result) && ($temp = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        $tm=time();
        $expires=$temp["expires"];
        $isAdminSession=FALSE;
        if ($temp["is_admin"] != 0)
           $isAdminSession=TRUE;

        if ($expires < ($tm+$time_extension))
          {
            // printf("EXPIRES : %d<br>\n",$expires);
            // printf("RENEWAL : %d<br>\n",($tm+$time_extension));

             $sql  = "UPDATE session set";
             $sql .= " expires=".($tm+$time_extension)." ";
             $result = exec_query($sql,$db);
             if ($result)
               {
                 return (0);
               }
          }
        else
          {
              // Expiration is Still More than "minutes" out - Nothing to do
            return (0);
          }
      }

    return (-1);
  }


function getSession($db, $sid, $mid)
  {
    $sql  = "SELECT * FROM session";
    $sql .= " WHERE session_id='$sid'";
    $sql .= " AND   member_id='$mid'";
    $result = exec_query($sql,$db);

    // dumpSQL($sql,mysql_error(),TRUE);

    if (($result) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        $expires = $myrow["expires"];
        $isAdminSession=FALSE;
        if ($myrow["is_admin"] != 0)
          $isAdminSession=TRUE;
        $tm=time();
        if ($expires > $tm)
          {
            if ($redirect)
              {
                closeSession($db,$sid,"Session Expired - Please Sign In","");
                exit;
              }
            return array(0, $isAdminSession);
          }

        // EXPIRED - DELETE IT
        $sql  = "DELETE FROM session";
        $sql .= " WHERE session_id='$sid'";
        $sql .= " AND member_id='$mid'";
        $result = exec_query($sql,$db);
      }
    return array(-1,FALSE);
  }


function isAdminSession($db, $sid, $mid)
  {
    $sql  = "SELECT is_admin FROM session";
    $sql .= " WHERE session_id='$sid'";
    $sql .= " AND   member_id='$mid'";
    $result = exec_query($sql,$db);
    if (($result) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        if ($myrow["is_admin"] != 0)
          return TRUE;
      }
    return FALSE;
  }


//=========================================================================================================
//
// MEMBERS
//
//=========================================================================================================
function getMemberInfo($db, $mid)
  {
    $sql  = "SELECT * from member";
    $sql .= " WHERE member_id='$mid'";
    $result = exec_query($sql,$db);
    if ($result && (mysql_num_rows($result)>0) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        mysql_free_result($result);
        return($myrow);
      }
    if ($result)
      mysql_free_result($result);
    return FALSE;
  }


function getMemberInfoForEmail($db, $email)
  {
    $sql  = "SELECT * from member";
    $sql .= " WHERE email='$email'";
    $result = exec_query($sql,$db);
    if ($result && (mysql_num_rows($result)>0) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        mysql_free_result($result);
        return($myrow);
      }
    if ($result)
      mysql_free_result($result);
    return FALSE;
  }


function getMemberName($db, $mid)
  {
    $name="";
    $sql  = "SELECT lastname, firstname FROM member ";
    $sql .= " WHERE member_id = '$mid'";
    $result = exec_query($sql,$db);
    if ($result && (mysql_num_rows($result)>0) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        mysql_free_result($result);
        $name=getMemberFullName($myrow);
      }
    return $name;
  }


function getMemberFullName($memberRecord)
  {
    $name=stripslashes($memberRecord["firstname"])." ".stripslashes($memberRecord["lastname"]);
    return $name;
  }


function getMemberFirstName($memberRecord)
  {
    $name=stripslashes($memberRecord["firstname"]);
    return $name;
  }


function isSystemUser($db, $mid)
  {
    $sql  = "SELECT member_id from member";
    $sql .= " WHERE member_id='$mid'";
    $sql .= " AND system > 0";
    $result = exec_query($sql,$db);
    if ($result && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        mysql_free_result($result);
        return(TRUE);
      }
    return FALSE;
  }


function doesMemberIdExist($db, $mid)
  {
    $sql  = "SELECT firstname from member";
    $sql .= " WHERE member_id='$mid'";
    $result = exec_query($sql,$db);
    if ($result && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        mysql_free_result($result);
        return(TRUE);
      }
    return FALSE;
  }


function getMemberIdFromEmail($db, $email)
  {
    $sql  = "SELECT member_id from member";
    $sql .= " WHERE email='$email'";
    $result = exec_query($sql,$db);
    if ($result && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        $mid=$myrow["member_id"];
        mysql_free_result($result);
        return($mid);
      }
    return FALSE;
  }


//=========================================================================================================
//
// MESSAGE QUEUE
//
//=========================================================================================================

function getMessageQueue($db,$message_id)
  {
    $sql = "SELECT * from messagequeue where message_id='$message_id'";
    $result = exec_query($sql,$db);
    if (($result) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        return($myrow);
      }
    return FALSE;
  }


function getMessageBox($db,$mid,$message_id)
  {
    $sql = "SELECT * from messagebox where member_id='$mid' AND  message_id='$message_id'";
    $result = exec_query($sql,$db);
    if (($result) && ($myrow = mysql_fetch_array($result, MYSQL_ASSOC)))
      {
        return($myrow);
      }
    return FALSE;
  }



function getMessage($db,$mid,$message_id)
  {
    $mbox = getMessageBox($db,$mid,$message_id);
    if (is_array($mbox))
      {
        $msgQ = getMessageQueue($db,$message_id);
        if (is_array($msgQ))
          {
            $msg = array_merge($mbox, $msgQ);
            return($msg);
          }
      }
    return FALSE;
  }




function dumpSQL($sql,$error,$quit)
  {
    printf("<br>SQL: %s<br>\n",$sql);
    printf("<br>ERR: %s<br>\n",$error);
    if ($quit)
      {
        exit;
      }
  }


//============================================================================ SQL LOGGING ======================
if (version_compare(phpversion(), "5.1.0", ">=")) {
       // you're on 5.1.0 or later
   // include("VLOG.php");
   // include("VLOG_QUIET.php");
} else {
}


function exec_query($sql,$db)
  {
    $result = mysql_query($sql,$db);

//      printf("<PRE>\n");
//      printf("SQL: %s\n",$sql);
//      $err=mysql_error();
//      if (strlen($err)>0)
//        {
//          printf("ERR: %s\n",mysql_error());
//        }
//      printf("</PRE>\n");


//  if (mysql_errno() != 0)
//    {
//      printf("SQL: %s\n",$sql);
//      $err=mysql_error();
//      if (strlen($err)>0)
//        {
//          printf("ERR: %s\n",mysql_error());
//        }
//    }

    return $result;
  }


function exec_fetch_array($result, $result_type=MYSQL_BOTH)
  {

    $row = mysql_fetch_array($result, $result_type);

    if (!is_array($row))
      {
        return $row;
      }

//  foreach($row AS $key => $data)
//    {
//      if (!isNumeric($key))
//        VLOG::printf("   %-20s => %s\n",$key,$data);
//    }

    return $row;
  }
?>
