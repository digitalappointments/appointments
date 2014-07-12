<?php
include_once("tgm_constants.inc");

//===================================================================================
$REMOTE_ADDR  = $_SERVER["REMOTE_ADDR"];
$QUERY_STRING = $_SERVER["QUERY_STRING"];
$REQUEST_URI  = $_SERVER["REQUEST_URI"];
$HTTP_HOST    = $_SERVER["HTTP_HOST"];
//===================================================================================

$common_included=TRUE;
$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$alpha_subset="ABCDEFHJKMNPRSTVWXY";
$hexcharacters="0123456789abcdef";
$map="abcdefghijklmnopqrstuvwxyz0123456789_";
$month_names = array("January", "February", "March", "April", "May", "June", "July",
                     "August", "September", "October", "November", "December");
$day_names   = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
srand((double)microtime()*1000000);  // Seed Randomizer

$SECONDS_IN_MINUTE = 60;
$SECONDS_IN_HOUR   = $SECONDS_IN_MINUTE * 60;
$SECONDS_IN_DAY    = $SECONDS_IN_HOUR   * 24;

$INCLUDE=TRUE;
$EXCLUDE=FALSE;
define("OMIT", FALSE);
define("EXCLUDE", FALSE);
define("NOT_EXCLUDE", TRUE);

// function isLocal()
//   {
//     // $HTTP_HOST=$_SERVER["HTTP_HOST"];
//     // if (is_integer(strpos($HTTP_HOST, "tjw")) || file_exists("c:\\WINDOWS") || file_exists("c:\\WINNT"))
//     //   {
//     //     return TRUE;
//     //   }
//     // return FALSE;
//
//     return(IS_LOCAL);
//   }


function dump_var($v)
  {
    if (is_array($v))
      {
        printf("<PRE>\n%s\n</PRE>\n",print_r($v,TRUE));
      }
    else
      printf("<PRE>\n%s\n</PRE>\n",$v);
  }


function trace_log($v,$append=TRUE)
  {
    if ($append)
      $fp=fopen("SYSTEM_TRACE.LOG","a");
    else
      $fp=fopen("SYSTEM_TRACE.LOG","w");

    if (is_array($v))
      fputs($fp,print_r($v,TRUE)."\n");
    else
      fputs($fp,sprintf("%s\n",$v));
    fclose($fp);
  }


function getFileInfo($file_name)
  {
    $fileName     = realpath($file_name);
    $dirName      = dirname($file_name);
    $baseName     = basename($file_name);
    $pathInfo     = pathinfo($file_name);
    $extension    = $pathInfo["extension"];
    $baseFileName = $pathInfo["filename"];

    $fileInfo = array(
                       "FileName"     => $fileName,
                       "DirName"      => $dirName,
                       "BaseName"     => $baseName,
                       "PathInfo"     => $pathInfo,
                       "Extension"    => $extension,
                       "BaseFileName" => $baseFileName
                     );

    if (FALSE)
      {
        printf("<PRE>\n");
        printf("\n%s\n",$file_name);
        printf("\n%s\n",print_r($fileInfo));
        printf("</PRE>\n");
      }
    return($fileInfo);
  }



function upperCaseWords($s)
  {
    $j=0;
    $t="";
    $tarray=explode(" ",$s);
    $count=count($tarray);
    for ($i=0; $i<$count; $i++)
      {
        $p = $tarray[$i];
        if (strlen($p)>0)
          {
            $p=ucfirst(strtolower($p));
            if ($j==0)
              $t=$p;
            else
              $t.=" ".$p;
            $j++;
          }
      }
    return $t;
  }


function ucfirst_only($str)
  {
    $s="";
    $len=strlen($str);
    if ($len == 1)
      $s=strtoupper($str);
    else
    if ($len > 1)
      $s=strtoupper(substr($str,0,1)).strtolower(substr($str,1));
    return $s;
  }


function quoted($str)   // SQL Statement Construction
  {
    return "'".addslashes($str)."'";
  }

function startsWith($source, $aString)
  {
    $src = strtolower($source);
    $str = strtolower($aString);
    $lensrc=strlen($src);
    $lenstr=strlen($str);
    if ($lenstr > $lensrc)
      return(FALSE);
    if ($str == $src)
      return(TRUE);
    if (substr($src,0,$lenstr) == $str)
      return(TRUE);
    return(FALSE);
  }


function endsWith($source, $aString)
  {
    $src = strtolower($source);
    $str = strtolower($aString);
    $lensrc=strlen($src);
    $lenstr=strlen($str);
    if ($lenstr > $lensrc)
      return(FALSE);
    if ($str == $src)
      return(TRUE);
    $pos = $lensrc - $lenstr;
    if (substr($src,$pos,$lenstr) == $str)
      return(TRUE);
    return(FALSE);
  }


function startsWithAsIs($source, $aString)
  {
    $src = $source;
    $str = $aString;
    $lensrc=strlen($src);
    $lenstr=strlen($str);
    if ($lenstr > $lensrc)
      return(FALSE);
    if ($str == $src)
      return(TRUE);
    if (substr($src,0,$lenstr) == $str)
      return(TRUE);
    return(FALSE);
  }


function padLeft($s, $len)
  {
    $slen=strlen($s);
    for ($i=$slen; $i<$len; $i++)
      $s=" ".$s;
    return $s;
  }

function padRight($s, $len)
  {
    $slen=strlen($s);
    for ($i=$slen; $i<$len; $i++)
      $s=$s." ";
    return $s;
  }


function endsWithAsIs($source, $aString)
  {
    $src = $source;
    $str = $aString;
    $lensrc=strlen($src);
    $lenstr=strlen($str);
    if ($lenstr > $lensrc)
      return(FALSE);
    if ($str == $src)
      return(TRUE);
    $pos = $lensrc - $lenstr;
    if (substr($src,$pos,$lenstr) == $str)
      return(TRUE);
    return(FALSE);
  }


function contains($source, $aString)
  {
    $src = strtolower($source);
    $str = strtolower($aString);
    $lensrc=strlen($src);
    $lenstr=strlen($str);
    if ($lenstr > $lensrc)
      return(FALSE);
    if ($str == $src)
      return(TRUE);
    if (is_integer(strpos($src, $str)))
      return(TRUE);
    return(FALSE);
  }

function isNumeric($token)
  {
    $len=strlen($token);
    if ($len==0) return(FALSE);
    for ($i=0; $i<$len; $i++)
      {
        $temp=substr($token,$i,1);
        if (!($temp >= "0" && $temp <= "9"))
          return(FALSE);
      }
    return(TRUE);
  }

function isAlphabetic($token)
  {
    $len=strlen($token);
    if ($len==0) return(FALSE);
    for ($i=0; $i<$len; $i++)
      {
        $temp=strtolower(substr($token,$i,1));
        if (!($temp >= "a" && $temp <= "z"))
          return(FALSE);
      }
    return(TRUE);
  }


function isAlphanumeric($token)
  {
    $len=strlen($token);
    if ($len==0) return(FALSE);
    for ($i=0; $i<$len; $i++)
      {
        $temp=strtolower(substr($token,$i,1));
        if (!($temp >= "a" && $temp <= "z") &&
            !($temp >= "0" && $temp <= "9"))
          return(FALSE);
      }
    return(TRUE);
  }

function containsNumeric($token)
  {
    $len=strlen($token);
    if ($len==0) return(FALSE);
    for ($i=0; $i<$len; $i++)
      {
        $temp=substr($token,$i,1);
        if ($temp >= "0" && $temp <= "9")
          return(TRUE);
      }
    return(FALSE);
  }

function formatPhone($phone)
  {
    $temp = strtolower($phone);
    $len=strlen($temp);
    $count=0;
    $ph="";
    for ($i=0; $i<$len; $i++)
      {
        $s=substr($temp,$i,1);
        if ($s >= "0" && ($s <= "9"))
          {
            $count++;
            $ph .= $s;
          }
      }
    $ph2="";
    $len=strlen($ph);
    $j=0;
    for ($i=$len-1; $i>=0; $i--)
      {
        $s=substr($ph,$i,1);
        $ph2 = $s.$ph2;
        if ($i > 0)
          {
            $j++;
            if ($j==4 || $j==7 || $j==10)
              $ph2 = "-".$ph2;
          }
      }
    return $ph2;
  }


function stripEOL($s)
  {
    while( ($len=strlen($s)) > 0 &&
           (substr($s,$len-1,1) == "\r" || (substr($s,$len-1,1) == "\n")))
      {
        $s=substr($s,0,$len-1);
      }
    return($s);
  }


function strip_slashes($s)
  {
    while (is_integer( strpos($s,"\\") ))
      $s=stripslashes($s);
    return $s;
  }


function add_slashes($s)
  {
    return(addslashes(strip_slashes($s)));
  }


function loadFileAsArray($filename)
  {
    $lines=0;
    if (!file_exists($filename))
      return FALSE;
    $fh = fopen($filename, "r");
    while (!feof($fh))
      {
        $buffer = stripEOL(fgets($fh, 4096));
        $ftext[$lines++]=$buffer;
      }
    fclose($fh);
    return $ftext;
  }


function listDirectory($dirName)
  {
    if (is_dir($dirName))
      {
        $handle=opendir($dirName);
        $count=0;
        while ($file = readdir($handle))
          {
            if ($file != "." && $file != "..")
              {
                $dirList[$count]=$file;
                $count++;
              }
          }
        closedir($handle);
      }
    return $dirList;
  }


function generateSession()
  {
   global $alpha_subset;
   $len=strlen($alpha_subset);
   $ts=getmicroseconds();
   return ($ts.substr($alpha_subset,rand(1,$len)-1,1).substr($alpha_subset,rand(1,$len)-1,1).substr($alpha_subset,rand(1,$len)-1,1).substr($alpha_subset,rand(1,$len)-1,1));
  }

function genRandomString($minlen, $maxlen)
  {
    global $alpha_subset;
    $alen=strlen($alpha_subset);
    $tlen=rand($minlen,$maxlen);
    $temp="";
    for ($i=0; $i<$tlen; $i++)
      {
        $temp .= substr($alpha_subset,rand(0,$alen-1),1);
      }
    return $temp;
  }


function numLines($filename)
  {
    if (!file_exists($filename))
      return(0);
    $lines=0;
    $fh = fopen($filename, "r");
    while (!feof($fh))
      {
        $buffer = stripEOL(fgets($fh, 4096));
        if (strlen($buffer) > 0)
            $lines++;
      }
    fclose($fh);
    return($lines);
  }


function getDigits($token)
  {
    $s="";
    $len=strlen($token);
    if ($len==0) return($s);
    for ($i=0; $i<$len; $i++)
      {
        $temp=substr($token,$i,1);
        if ($temp >= "0" && $temp <= "9")
           $s .= $temp;
      }
    return($s);
  }


function getAlphabetic($token)
  {
    $s="";
    $len=strlen($token);
    if ($len==0) return($s);
    for ($i=0; $i<$len; $i++)
      {
        $temp=substr($token,$i,1);
        if (($temp >= "a" && $temp <= "z") ||
            ($temp >= "A" && $temp <= "Z") )
           $s .= $temp;
      }
    return($s);
  }


function getAlphanumeric($token)
  {
    $s="";
    $len=strlen($token);
    if ($len==0) return($s);
    for ($i=0; $i<$len; $i++)
      {
        $temp=substr($token,$i,1);
        if (($temp >= "a" && $temp <= "z") ||
            ($temp >= "A" && $temp <= "Z") ||
            ($temp >= "0" && $temp <= "9") )
           $s .= $temp;
      }
    return($s);
  }


function countCharacters($string, $chr=" ")
 {
   $count=0;
   $len = strlen($string);
   for ($i=0; $i<$len; $i++)
     {
       $s = substr($string,$i,1);
       if ($s == $chr) $count++;
     }
   return ($count);
 }


function stripa($string, $chr=" ")
 {
   $result="";
   $len = strlen($string);
   for ($i=0; $i<$len; $i++)
     {
       $s = substr($string,$i,1);
       if ($s != $chr)
         {
           $result .= $s;
         }
     }
   return ($result);
 }


function stripl($string, $chr=" ")
 {
   $j=0;
   $len = strlen($string);
   for ($i=0; $i<$len; $i++)
     {
       $s = substr($string,$i,1);
       if ($s != $chr)
         {
           return substr($string,$i,($len-$j));
         }
       $j++;
       if ($j == $len)
         {
           return "";
         }
     }
 }


function stript($string, $chr=" ")
 {
   $len1 = strlen($string);
   $len2 = $len1;
   for ($i=$len2-1; $i>=0; $i--)
     {
       if (substr($string,$i,1) == $chr)
         $len2--;
       else
         break;
     }

   if ($len2 == 0)
       return "";
   if ($len2 != $len1)
       return substr($string,0,$len2);
   return $string;
 }


function striplt($string, $chr=" ")
 {
   $result = stripl($string, $chr);
   $result = stript($result, $chr);
   return ($result);
 }


function stripchr($string,$ch)
 {
   $result="";
   $len = strlen($string);
   for ($i=0; $i<$len; $i++)
     {
       $s = substr($string,$i,1);
       if ($s != $ch)
         {
           $result .= $s;
         }
     }
   return ($result);
 }


function send_mail_direct($toName, $toEmail, $fromName, $fromEmail, $subject, $message)
  {
    $recipient       = "\"$toName\" <$toEmail>";
    $sender          = "\"$fromName\" <$fromEmail>";
    $extraHeaders    = "From: ".$sender."\n";
    $extraHeaders   .= "Reply-To: ".$sender."\n";

    $message = str_replace("\\",  "", $message);
    $subject = str_replace("\\",  "", $subject);


    $message .= "\r\n";
    $message .= " !--------------------------------------------------------!\r\n";
    $message .= " ! To Reply to this message, click on the CONTACT US link !\r\n";
    $message .= " ! at the top of the TGM Website  ( DO NOT reply by email)!\r\n";
    $message .= " !--------------------------------------------------------!\r\n";
    $message .= "\r\n";



    if (IS_LOCAL)
      {
        $fp = fopen("\\SENDLIST.OUT", "a");
        fputs($fp, "\n\n------------------------------------\n");
        fputs($fp, "To: $recipient\n");
        fputs($fp, "Subject: $subject\n");
        fputs($fp, "$extraHeaders\n\n");
        fputs($fp, "$message\n");
        fclose($fp);
        $result=TRUE;
      }
    else
      {
        $result = mail($recipient, $subject, $message."\n", $extraHeaders);

        /** --- SEND TO WEBTRIBUNE --- **/
        // $recipient = "\"$toName\" <ditto@webtribune.com>";
        // $result = mail($recipient, $subject, $message."\n", $extraHeaders);

      }
    return($result);
  }




function sendMessageFile($messageFile, $toName, $toEmail, $vars)
  {
    if (!file_exists($messageFile))
      return FALSE;

    $msgData   = loadFileAsArray($messageFile);
    $fromName  = "";
    $fromEmail = "";
    $subject   = "";
    $message   = "";
    $inMessage = FALSE;
    $cnt=0;
    $result=FALSE;
    if (is_array($msgData))
      {
        $message="";
        $numLines=count($msgData);
        for ($i=0; $i<$numLines; $i++)
          {
            if ($inMessage)
              {
                $message .= $msgData[$i]."\n";
              }
            else
              {
                if ($msgData[$i] == "MESSAGE")
                  $inMessage = TRUE;
                else
                  {
                    list($key,$value) = split("=",$msgData[$i]);
                    if ($key == "FROMNAME")
                      $fromName=$value;
                    if ($key == "FROMEMAIL")
                      $fromEmail=$value;
                    if ($key == "SUBJECT")
                      $subject=$value;
                  }
              }
          }
        if (!is_array($vars))
          $vars=array();
            // Supply Standard System Variables if not Provided
        if (!isset($vars["%date%"]))   $vars["%date%"] = getDateToday();
        if (!isset($vars["%time%"]))   $vars["%time%"] = getTimeNow()." MST";

        if (is_array($vars))
          {
            foreach($vars as $key=>$data)
             {
               if (!is_integer(strpos($key,"%")))
                  $key="%".$key."%";
               $subject = str_replace($key, $data, $subject);
               $message = str_replace($key, $data, $message);
             }
          }

        $result = send_mail_direct($toName, $toEmail, $fromName, $fromEmail, $subject, $message);
      }

    return($result);
  }



function send_mail_remote($toName, $toEmail, $fromName, $fromEmail, $subject, $message)
  {
    $remote_mail_host = REMOTE_SENDMAIL_HOST;
    $remote_mail_uri  = REMOTE_SENDMAIL_URI;

    $connection = fsockopen ($remote_mail_host, 80, $errorNumber, $errorString, 10);

    if ($connection)
      {
        $mailinfo  = "";
        $mailinfo .= "ToName=".urlencode($toName)."&";
        $mailinfo .= "ToEmail=".urlencode($toEmail)."&";
        $mailinfo .= "FromName=".urlencode($fromName)."&";
        $mailinfo .= "FromEmail=".urlencode($fromEmail)."&";
        $mailinfo .= "Subject=".urlencode($subject)."&";
        $mailinfo .= "Message=".urlencode($message);

        $contentLength = strlen($mailinfo);

        // printf("REMOTE_MAIL_HOST: %s<br>\n",$remote_mail_host);
        // printf("REMOTE_MAIL_URI : %s<br>\n",$remote_mail_uri);

        //set non-blocking mode
        set_socket_blocking($connection, false);

        $headers  = "POST ".$remote_mail_uri." HTTP/1.0\r\n";
        $headers .= "Host: ".$remote_mail_host."\r\n";
        $headers .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $headers .= "Content-Length: $contentLength\r\n";
        $headers .= "\r\n";
        $headers .= $mailinfo;

        // printf("%s\n",$headers);

        fputs ($connection, $headers);

        $headerStart = 0;
        $headerEnd = 0;

        while (!feof($connection))
          {
            $currentLine = fgets ($connection, 1024);
            // echo $currentLine;
            if ($headerEnd)
              {
                // this is the html from the page
                // echo $currentLine;
              }
            else if ( ereg("^HTTP", $currentLine) )
              {
                // came to the start of the header
                $headerStart = 1;
              }
            else if ( $headerStart && ereg("^[\n\r\t ]*$", $currentLine) )
              {
                //came to the end of the header
                $headerEnd = 1;
              }
            else
              {
                // this is the header, if you want it...
              }
          }

        fclose ($connection);
        return TRUE;
      }

    return FALSE;
  }



function encode($s)
 {
   global $map;
   $ord_0 = ord("0");
   $qs=strtolower($s);
   $buf="";

 //-- printf("Encode: '$s'\n");

   $len=strlen($qs);
   $i=0;
   while ($i<$len)
     {
       $ch=substr($qs,$i,1);
       $inx = (int) strpos($map, $ch);
       if ($inx >= 0 && $inx < strlen($map))
         {
           if ($inx < 10)
             $buf .= ".";
           $buf .= $inx;
         }
       $i++;
     }
   return("9".$buf);
 }



function decode($s)
 {
   global $map;
   $ord_0 = ord("0");
   $qs=strtolower($s);
   $buf="";

  //-- printf("Decode: '$s'\n");

   $len=strlen($qs);
   if ($len > 0 && (substr($qs,0,1) == '9'))
     $qs = substr($qs,1);
   else
     if (is_integer(strpos($s,"%")))
       return(hex2ascii($s));
     else
         return $s;

   $i=0;
   while ($i<$len)
     {
       $ch=substr($qs,$i,1);
       $i++;
       if ($ch==".")
         {
           $c1=substr($qs,$i,1);
           $n1=ord($c1)-$ord_0;
           $inx=$n1;
     // printf("  A: $c1  $n1  $inx\n");
           if ($inx >= 0 && ($inx < strlen($map)))
             $buf .= substr($map,$inx,1);
           $i++;
         }
       else
         {
           if ($ch >= "0" && $ch <= "9")
             {
               $c1=$ch;
               $c2=substr($qs,$i,1);
               $n1=ord($c1)-$ord_0;
               $n2=ord($c2)-$ord_0;
               $inx = ($n1*10) + $n2;
     // printf("  B: $c1 $c2    $n1 $n2    $inx\n");
               if ($inx >= 0 && ($inx < strlen($map)))
                  $buf .= substr($map,$inx,1);
               $i++;
             }
         }
     }
   return($buf);
 }



function hex2ascii($s)
 {
   if (is_integer(strpos($s,"%")))
     {
        $ord_0 = ord("0");
        $ord_a = ord("a");
        $qs=strtolower($s);
        $buf="";
        $len=strlen($qs);
        $i=0;
        while ($i<$len)
          {
            $ch=substr($qs,$i,1);
            if ($ch=="%")
              {
                $i++;
                $c1=substr($qs,$i,1);
                $i++;
                $c2=substr($qs,$i,1);
                $i++;
                if ($c1 >= "0" && ($c1 <= "9"))
                  $n1=ord($c1)-$ord_0;
                else
                  $n1=ord($c1)-$ord_a+10;
                if ($c2 >= "0" && ($c2 <= "9"))
                  $n2=ord($c2)-$ord_0;
                else
                  $n2=ord($c2)-$ord_a+10;
                $buf .= chr(($n1 * 16) + $n2);
              }
            else
              {
                $buf .= $ch;
                $i++;
              }
          }
        return($buf);
     }
   else
     {
       return(decode($s));
     }
 }


function ascii2hex($s)
 {
   global $hexcharacters;
   $buf="";
   $len=strlen($s);
   $i=0;
   while ($i<$len)
     {
       $ch=substr($s,$i,1);
       $i++;
       $n1 = (int)(ord($ch) / 16);
       $n2 = (int)(ord($ch)) - ($n1*16);
       $buf .= "%".substr($hexcharacters,$n1,1).substr($hexcharacters,$n2,1);
     }
   return($buf);
 }



function toHexString($s)
 {
   global $hexcharacters;
   $buf="";
   $len=strlen($s);
   $i=0;
   while ($i<$len)
     {
       $ch=substr($s,$i,1);
       $i++;
       $n1 = (int)(ord($ch) / 16);
       $n2 = (int)(ord($ch)) - ($n1*16);
       $buf .= substr($hexcharacters,$n1,1).substr($hexcharacters,$n2,1);
     }
   return($buf);
 }


function fromHexString($s)
 {
   global $hexcharacters;
   $buf="";
   $s2=strtolower($s);
   $len=strlen($s2);
   if ($len % 2 != 0) return("");
   $i=0;
   while ($i<$len)
     {
       $ch=substr($s2,$i,1);
       $c1=strpos($hexcharacters,$ch);
       if (!is_integer($c1)) return("");
       $i++;
       $ch=substr($s2,$i,1);
       $c2=strpos($hexcharacters,$ch);
       if (!is_integer($c2)) return("");
       $i++;
       $ord = ((int)($c1) * 16) + (int)($c2);
       $buf .= chr($ord);
     }
   return($buf);
 }


function booleanToString($b)
  {
    if ($b)
      return("TRUE");
    return("FALSE");
  }


function asTrueFalse($b,$uppercase=FALSE)
  {
    if ($uppercase)
      {
        if ($b) return("TRUE");
        return("FALSE");
      }
    if ($b) return("True");
    return("False");
  }


function asYesNo($b,$uppercase=FALSE)
  {
    if ($uppercase)
      {
        if ($b) return("YES");
        return("NO");
      }
    if ($b) return("Yes");
    return("No");
  }


function isValidID($s)
  {
    $slen=strlen($s);
    if ($slen < 6)  return(FALSE);
    if ($slen > 20) return(FALSE);
    for ($i=0; $i<$slen; $i++)
      {
        $temp=substr($s,$i,1);
         if (! (($temp == '_')                 ||
                ($temp >= 'a' && $temp <= 'z') ||
                ($temp >= 'A' && $temp <= 'Z') ||
                ($temp >= '0' && $temp <= '9')) )
           {
             return(FALSE);
           }
      }
    return(TRUE);
  }


function isValidPW($s)
  {
    $slen=strlen($s);
    if ($slen < 6)  return(FALSE);
    if ($slen > 20) return(FALSE);
    $digits=0;
    for ($i=0; $i<$slen; $i++)
      {
        $temp=substr($s,$i,1);
         if (! (($temp >= 'a' && $temp <= 'z') ||
                ($temp >= 'A' && $temp <= 'Z') ||
                ($temp >= '0' && $temp <= '9')) )
           {
             return(FALSE);
           }
         if ($temp >= '0' && $temp <= '9') $digits++;
      }
    if ($digits < 1) return(FALSE);
    return(TRUE);
  }


function isValidEmail($email)
  {
    $STATUS=0;
    $em=strtolower($email);

    $len=strlen($em);
    for ($i=0; $i<$len; $i++)
      {
        $s = substr($em,$i,1);
        if ( ($s >= "0" && $s <= "9")  ||
             ($s >= "a" && $s <= "z")  ||
             ($s == ".")               ||
             ($s == "-")               ||
             ($s == "_")               ||
             ($s == "@") )
          {

          }
        else
          {
            $STATUS=2;
            break;
          }
      }

    if ($STATUS == 0)
      {
        $em = explode("@",$email);
        if (!is_array($em))
          $STATUS=4;
        else
        if (count($em) != 2)
          $STATUS=6;
        else
          {
            $domain = $em[1];
            $fc = substr($domain,0,1);
            $lc = substr($domain,strlen($domain)-1,1);

            if (!isAlphanumeric($fc))
              $STATUS=7;
            else
            if (!isAlphanumeric($lc))
              $STATUS=8;
          }
      }

    // printf("%s   %2d\n",$email,$STATUS);

    if ($STATUS==0)
      return TRUE;
    return FALSE;
  }



//--------------------------------------------------------------------
// DATE/TIME Fuctions
//--------------------------------------------------------------------

 /*****
    a=Wed                      %a - abbreviated weekday name according to the current locale
    A=Wednesday                %A - full weekday name according to the current locale
    b=Apr                      %b - abbreviated month name according to the current locale
    B=April                    %B - full month name according to the current locale
    c=04/19/00 21:28:06        %c - preferred date and time representation for the current locale
    d=19                       %d - day of the month as a decimal number (range 00 to 31)
    H=21                       %H - hour as a decimal number using a 24-hour clock (range 00 to 23)
    I=09                       %I - hour as a decimal number using a 12-hour clock (range 01 to 12)
    j=110                      %j - day of the year as a decimal number (range 001 to 366)
    m=04                       %m - month as a decimal number (range 1 to 12)
    M=28                       %M - minute as a decimal number
    p=PM                       %p - either `am' or `pm' according to the given time value, or the corresponding strings for the current locale
    S=06                       %S - second as a decimal number
    U=16                       %U - week number of the current year as a decimal number, starting with the first Sunday as the first day of the first week
    W=16                       %W - week number of the current year as a decimal number, starting with the first Monday as the first day of the first week
    w=3                        %w - day of the week as a decimal, Sunday being 0
    x=04/19/00                 %x - preferred date representation for the current locale without the time
    X=21:28:06                 %X - preferred time representation for the current locale without the date
    y=00                       %y - year as a decimal number without a century (range 00 to 99)
    Y=2000                     %Y - year as a decimal number including the century
    Z=Eastern Daylight Time    %Z - time zone or name or abbreviation
  *****/



function timestampFromDateArray($dateArray)
  {
    $timestamp = mktime(
              0,
              0,
              0,
              $dateArray["month"],
              $dateArray["day"],
              $dateArray["year"] );
    return $timestamp;
  }

function timestampFromDateTimeArray($datetimeArray)
  {
    $timestamp = mktime(
              $datetimeArray["hour"],
              $datetimeArray["minute"],
              $datetimeArray["second"],
              $datetimeArray["month"],
              $datetimeArray["day"],
              $datetimeArray["year"] );
    return $timestamp;
  }


function timestampFromDateString($dateAsString)
  {
    $dateArray = dateToArray($dateAsString);
    $timestamp = timestampFromDateArray($dateArray);
    return $timestamp;
  }


function adjustTimestamp($tm)
  {
    $dt = formatDate($tm);
    $timestamp = timestampFromDateString($dt);
    return $timestamp;
  }

function secondsSinceMidnight($tm=0)
  {
    if ($tm==0) $tm=time();
    return ($tm - timeAtMidnightToday($tm));
  }

function secondsToMidnight($tm=0)
  {
    if ($tm==0) $tm=time();
    return (timeAtMidnightTomorrow($tm) - $tm);
  }

function timeAtMidnightToday($tm=0)
  {
    if ($tm==0) $tm=time();
    $adjusted = adjustTimestamp($tm);
    return $adjusted;
  }

function timeAtMidnightTomorrow($tm=0)
  {
    return timeAtMidnightToday($tm)+86400;
  }

function getDateToday()
  {
    $tm=time();
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $dateToday = sprintf("%04d-%02d-%02d",$yy,$mm,$dd);
    return($dateToday);
  }

function dateAsText($dateArray=FALSE)
  {
    if (!$dateArray)
      $dateArray=getDateTodayAsArray();

    global $month_names;
    $mm=$dateArray["month"];
    if (!($mm >= 1 && $mm <=12))
      $mm = 1;
    $dd=$dateArray["day"];
    $yy=$dateArray["year"];
    $dateText = sprintf("%s %s, %s",$month_names[$mm-1],$dd,$yy);
    return($dateText);
  }

function timeAsText($dateArray=FALSE)
  {
    if (!$dateArray)
      $dateArray=getDateTimeAsArray();

    $hr=$dateArray["hour"];
    $mn=$dateArray["minute"];

    $ampm="am";
    if ($hr >= 12)
      {
        $ampm="pm";
        $hr-=12;
      }
    if ($hr==0) $hr=12;

    $timeText = sprintf("%2d:%02d %s",$hr,$mn,$ampm);
    return($timeText);
  }

function getTimeNow($incsec=false)
  {
    $tm=time();
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    if ($incsec)
      {
        $sc=strftime("%S",$tm);
        $timeNow  = sprintf("%02d:%02d:%02d",$hr,$mn,$sc);
      }
    else
       $timeNow   = sprintf("%02d:%02d",$hr,$mn);
    return($timeNow);
  }

function getMonthEndDateAsArray($dateArray=FALSE)
  {
    if (is_array($dateArray))
      $dt = $dateArray;
    else
      $dt = getDateTodayAsArray();
    $calData=calendar($dt);
    $dim=$calData["DaysInMonth"];
    $dt["day"]=$dim;
    return($dt);
  }

function getPreviousMonthEndDateAsArray()
  {
    $today = getDateTodayAsArray();
    $prevMonth=calStepMonths(-1,$today);
    return(getMonthEndDateAsArray($prevMonth));
  }


function getLocalTimeNow($incsec=false)
  {
    $tm=time();
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    $tz=strftime("%Z",$tm);
    if ($incsec)
      {
        $sc=strftime("%S",$tm);
        $timeNow   = sprintf("%02d:%02d:%02d %s",$hr,$mn,$sc,$tz);
      }
    else
       $timeNow   = sprintf("%02d:%02d %s",$hr,$mn,$tz);
    return($timeNow);
  }


function getDateTime($incsec=false)
  {
    $tm=time();
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    if ($incsec)
      {
        $sc=strftime("%S",$tm);
        $dateTime = sprintf("%04d-%02d-%02d %02d:%02d:%02d",$yy,$mm,$dd,$hr,$mn,$sc);
      }
    else
       $dateTime = sprintf("%04d-%02d-%02d %02d:%02d",$yy,$mm,$dd,$hr,$mn);

    return($dateTime);
  }


function getPrintableDateToday()
  {
    $tm=time();
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $dateToday = sprintf("%02d/%02d/%04d",$mm,$dd,$yy);
    return($dateToday);
  }


function getPrintableDateTime($incsec=false)
  {
    $tm=time();
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    if ($incsec)
      {
        $sc=strftime("%S",$tm);
        $dateTime = sprintf("%02d/%02d/%04d %02d:%02d:%02d",$mm,$dd,$yy,$hr,$mn,$sc);
      }
    else
        $dateTime = sprintf("%02d/%02d/%04d %02d:%02d",$mm,$dd,$yy,$hr,$mn);
    return($dateTime);
  }


function formatLocalTime($incsec=false)
  {
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    $tz=strftime("%Z",$tm);
    if ($incsec)
      {
        $sc=strftime("%S",$tm);
        $time = sprintf("%02d:%02d:%02d %s",$hr,$mn,$sc,$tz);
      }
    else
        $time = sprintf("%02d:%02d %s",$hr,$mn,$tz);
    return($time);
  }

function dateFormatter($date, $monthOnly=FALSE, $abbreviateMonth=TRUE)
  {
    $mdate="";
    if (($monthOnly) && is_array($date)
                        && isset($date["year"])
                        && isset($date["month"]))
      {
        $yy = (int) $date["year"];
        $mm = (int) $date["month"];
        if ($abbreviateMonth)
          $month=getAbbrevMonthName($mm);
        else
          $month=getMonthName($mm);
        $mdate = $month.", ".$yy;
      }
    else
    if (is_array($date) && isset($date["year"])
                        && isset($date["month"])
                        && isset($date["day"]))
      {
        $yy = (int) $date["year"];
        $mm = (int) $date["month"];
        $dd = (int) $date["day"];
        if ($abbreviateMonth)
          $month=getAbbrevMonthName($mm);
        else
          $month=getMonthName($mm);
        $mdate = $month." ".$dd.", ".$yy;
      }
    return $mdate;
  }

function formatDate($tm)
  {
    if ($tm==0) return "null";
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $date = sprintf("%04d-%02d-%02d",$yy,$mm,$dd);
    return($date);
  }


function formatTime($tm, $incsec=false)
  {
    if ($tm==0) return "null";
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    if ($incsec)
      {
        $sc=strftime("%S",$tm);
        $time = sprintf("%02d:%02d:%02d",$hr,$mn,$sc);
      }
    else
        $time = sprintf("%02d:%02d",$hr,$mn);
    return($time);
  }


function formatDateTime($tm, $incsec=false)
  {
    if ($tm==0) return "null";
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);

    if ($incsec)
      {
        $sc=strftime("%S",$tm);
        $dateTime = sprintf("%04d-%02d-%02d %02d:%02d:%02d",$yy,$mm,$dd,$hr,$mn,$sc);
      }
    else
        $dateTime = sprintf("%04d-%02d-%02d %02d:%02d",$yy,$mm,$dd,$hr,$mn);
    return($dateTime);
  }


function getmicroseconds($asFloat=FALSE)
  {
    $t=gettimeofday();
    $t2=substr((sprintf("%06d", $t['usec'])."000000"),0,6);
    $ms = (double) ($t['sec'].$t2);
    if ($asFloat)
       return $ms;
    return sprintf("%1.0f",$ms);
  }

function micFormatDate($ms)
  {
    $tm=(int) ((double) $ms / (double) 1000000);
    return(formatDate($tm));
  }

function micFormatTime($ms, $incsec=false)
  {
    $tm = ((double) $ms / (double) 1000000);
    return(formatTime(toInteger($tm), $incsec));
  }

function micFormatDateTime($ms, $incsec=false)
  {
    $tm = ((double) $ms / (double) 1000000);
    return(formatDateTime(toInteger($tm), $incsec));
  }



function getmilliseconds($asFloat=FALSE)
  {
    $t=gettimeofday();
    $t2=substr((sprintf("%06d", $t['usec'])."000"),0,3);
    $ms = (double) ($t['sec'].$t2);
    if ($asFloat)
       return $ms;
    return sprintf("%1.0f",$ms);
  }


function toInteger($bint)
  {
    return sprintf("%1.0f",(double) $bint);
  }


function msFormatDate($ms)
  {
    $tm=(int) ((double) $ms / (double) 1000);
    return(formatDate($tm));
  }

function msFormatTime($ms, $incsec=false)
  {
    $tm = ((double) $ms / (double) 1000);
    $dif= $ms - ($tm * (double) 1000);
    return(formatTime(toInteger($tm), $incsec).".$dif");
  }

function msFormatDateTime($ms, $incsec=false)
  {
    $tm = ((double) $ms / (double) 1000);
    $dif= $ms - ($tm * (double) 1000);
    // printf("MS=$ms  SEC=$tm  DIF=$dif\n");
    return(formatDateTime(toInteger($tm), $incsec).".$dif");
  }

function msFromDateArray($dateArray)
  {
    $timestamp = (double) mktime(
              0,
              0,
              0,
              $dateArray["month"],
              $dateArray["day"],
              $dateArray["year"] ) * (double) 1000;
    // printf("TIMESTAMP = %s  %s",$timestamp,toInteger($timestamp));
    return (toInteger($timestamp));
  }


function msFromDateTimeArray($datetimeArray)
  {
    $timestamp = (double) mktime(
              $datetimeArray["hour"],
              $datetimeArray["minute"],
              $datetimeArray["second"],
              $datetimeArray["month"],
              $datetimeArray["day"],
              $datetimeArray["year"] ) * (double) 1000;
    // printf("TIMESTAMP = %s  %s",$timestamp,toInteger($timestamp));
    return (toInteger($timestamp));
  }


function msFromDateString($dateAsString)
  {
    $dateArray = dateToArray($dateAsString);
    return (msFromDateArray($dateArray));
  }


function msFromStartOfDay()
  {
    $temp=getDateTimeAsArray();
    $ts2=getmilliseconds(TRUE);
    $ts1 = (double) mktime(
              0,
              0,
              0,
              $temp["month"],
              $temp["day"],
              $temp["year"]) * (double) 1000;
    $sdif=$ts2-$ts1;
    // printf("TS2=%s  TS1=%s  DIF=%s\n",toInteger($ts2),toInteger($ts1),toInteger($sdif));
    return (toInteger($sdif));
  }


function msFromStartOfMonth()
  {
    $temp=getDateTodayAsArray();
    $temp["day"]=1;
    $ts2 = getmilliseconds(TRUE);
    $ts1 = (double) mktime(
              0,
              0,
              0,
              $temp["month"],
              1,
              $temp["year"]) * (double) 1000;
    $sdif=$ts2-$ts1;
    // printf("TS2=%s  TS1=%s  DIF=%s\n",toInteger($ts2),toInteger($ts1),toInteger($sdif));
    return (toInteger($sdif));
  }

function msFromStartOfYear()
  {
    $temp=getDateTodayAsArray();
    $ts2 = getmilliseconds(TRUE);
    $ts1 = (double) mktime(
              0,
              0,
              0,
              1,
              1,
              $temp["year"]) * (double) 1000;
    $sdif=$ts2-$ts1;
    // printf("TS2=%s  TS1=%s  DIF=%s\n",toInteger($ts2),toInteger($ts1),toInteger($sdif));
    return (toInteger($sdif));
  }

function getDateTodayAsArray()
  {
    $tm=time();
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $dateArray["month"] = (int) $mm;
    $dateArray["day"]   = (int) $dd;
    $dateArray["year"]  = (int) $yy;
    return $dateArray;
  }


function getTimeNowAsArray()
  {
    $tm=time();
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    $sc=strftime("%S",$tm);
    $timeArray["hour"]   = (int) $hr;
    $timeArray["minute"] = (int) $mn;
    $timeArray["second"] = (int) $sc;
    return $timeArray;
  }


function getDateTimeAsArray()
  {
    return(getDateTimeFromSecondsAsArray(time()));
  }


function getDateArrayFromInts($mm, $dd, $yy)
  {
    $dateArray["month"] = (int) $mm;
    $dateArray["day"]   = (int) $dd;
    $dateArray["year"]  = (int) $yy;
    return $dateArray;
  }


function getDateTimeArrayFromInts($mm, $dd, $yy, $hr, $mn, $sc)
  {
    $dtmArray["month"]  = (int) $mm;
    $dtmArray["day"]    = (int) $dd;
    $dtmArray["year"]   = (int) $yy;
    $dtmArray["hour"]   = (int) $hr;
    $dtmArray["minute"] = (int) $mn;
    $dtmArray["second"] = (int) $sc;
    return $dtmArray;
  }


function getDateTimeFromSecondsAsArray($tm)
  {
    $mm=strftime("%m",$tm);
    $dd=strftime("%d",$tm);
    $yy=strftime("%Y",$tm);
    $hr=strftime("%H",$tm);
    $mn=strftime("%M",$tm);
    $sc=strftime("%S",$tm);
    $dtmArray["month"]  = (int) $mm;
    $dtmArray["day"]    = (int) $dd;
    $dtmArray["year"]   = (int) $yy;
    $dtmArray["hour"]   = (int) $hr;
    $dtmArray["minute"] = (int) $mn;
    $dtmArray["second"] = (int) $sc;
    return $dtmArray;
  }


function dateArrayToString($dateArray)
  {
    $dt  = sprintf("%04d-%02d-%02d",
           $dateArray["year"], $dateArray["month"], $dateArray["day"]);
    return $dt;
  }


function timeArrayToString($timeArray)
  {
    $dt = sprintf("%02d:%02d:%02d",
           $timeArray["hour"], $timeArray["minute"], $timeArray["second"]);
    return $dt;
  }


function dateTimeArrayToString($dtmArray)
  {
    $dtm = sprintf("%04d-%02d-%02d %02d:%02d:%02d",
           $dtmArray["year"], $dtmArray["month"], $dtmArray["day"],
           $dtmArray["hour"], $dtmArray["minute"], $dtmArray["second"]);
    return $dtm;
  }


function getDateTimeAsSeconds()
  {
    return(time());
  }


function dateToArray($dateAsString)
  {
    list($yy,$mm,$dd) = split("-", $dateAsString);
    $dateArray["month"]  = (int)$mm;
    $dateArray["day"]    = (int)$dd;
    $dateArray["year"]   = (int)$yy;
    return $dateArray;
  }


function timeToArray($timeAsString)
  {
    list($hr,$mn,$sc) = split(":", $timeAsString);
    $timeArray["hour"]    = (int)$hr;
    $timeArray["minute"]  = (int)$mn;
    $timeArray["second"]  = (int)$sc;
    return $timeArray;
  }


function dateTimeToArray($dtmAsString)
  {
    list($dt, $tm) = split(" ", $dtmAsString);
    $da = dateToArray($dt);
    $ta = timeToArray($tm);
    $dtmArray["month"]  = $da["month"];
    $dtmArray["day"]    = $da["day"];
    $dtmArray["year"]   = $da["year"];
    $dtmArray["hour"]   = $ta["hour"];
    $dtmArray["minute"] = $ta["minute"];
    $dtmArray["second"] = $ta["second"];
    return $dtmArray;
  }


function getElapsedTimeAsArray($tm)
 {
   $now=time();
   if ($now > $tm)
     {                  /* Past Time */
       $elapsed["past"]=TRUE;
       $elapsed["future"]=FALSE;
       $dif=$now - $tm;
     }
   else
     {                  /* Future Time */
       $elapsed["past"]=FALSE;
       $elapsed["future"]=TRUE;
       $dif=$tm - $now;
     }

   $secInMinute = 60;
   $secInHour   = $secInMinute * 60;
   $secInDay    = $secInHour   * 24;

   $days = $dif / $secInDay;
   $dif  = $dif % $secInDay;

   $hr   = $dif / $secInHour;
   $dif  = $dif % $secInHour;

   $mn   = $dif / $secInMinute;
   $dif  = $dif % $secInMinute;
   $sc   = $dif;

// printf("           Days    = %d\n",$days);
// printf("           Hours   = %d\n",$hr);
// printf("           Minutes = %d\n",$mn);
// printf("           Seconds = %d\n",$sc);

// $elapsed["days"]    = "".(int)($days);
// $elapsed["hours"]   = "".(int)(($hr);
// $elapsed["minutes"] = "".(int)(($mn);
// $elapsed["seconds"] = "".(int)(($sc);

   $elapsed["days"]    = (int) $days;
   $elapsed["hours"]   = (int) $hr;
   $elapsed["minutes"] = (int) $mn;
   $elapsed["seconds"] = (int) $sc;

   return ($elapsed);
 }





//--------------------------------------------------------------------
// CALENDAR  Fuctions
//--------------------------------------------------------------------

function getMonthName($mm)
 {
   global $month_names;
   if ($mm >= 1 && ($mm <= 12))
     {
       return $month_names[$mm-1];
     }
   return "";
 }


function getDayName($dd)
 {
   global $day_names;
   if ($dd >= 0 && ($dd <= 6))
     {
       return $day_names[$dd];
     }
   return "";
 }


function getAbbrevMonthName($mm)
 {
   global $month_names;
   if ($mm >= 1 && ($mm <= 12))
     {
       return substr($month_names[$mm-1],0,3);
     }
   return "";
 }


function getAbbrevDayName($dd)
 {
   global $day_names;
   if ($dd >= 0 && ($dd <= 6))
     {
       return substr($day_names[$dd],0,3);
     }
   return "";
 }


//----------------------------------------------
// Subroutine: isLeapYear
//
// answer TRUE/FALSE whether supplied year is a
// leap year.
//
// Args   :  int  Year
// Returns:  TRUE id leapyear  otherwise FALSE
//----------------------------------------------
function isLeapYear($yy)
 {
   if (($yy % 400) == 0)
    { return TRUE; }   //-- IS a leapyear
   if (($yy % 100) == 0)
    { return FALSE; }  //-- IS NOT a leapyear
   if (($yy % 4) == 0)
    { return TRUE; }   //-- IS a leapyear
   return FALSE;       //-- Otherwise IS NOT a leapyear
 }



//-----------------------------------------------
// Subroutine: calendar
//
// Given a DateTime Array (or DateArray), answer
//    - (int) the DayofWeek(0-6)
//    - (int) the number of days in the month
//    - (int) the days since 01-01-1900
//
// Args   :  Date or DateTime (associative Array)
// Returns Array:
//      (int) DayOfWeek (0-6)
//      (int) DaysInMonth
//      (int) DaysSince1900
//-----------------------------------------------
function calendar($dtm)
 {
   $lycount = 0;
   $y     = 0;
   $month = 0;
   $day   = 0;
   $year  = 0;
   $dim   = 0;
   $dow   = 0;
   $s1900 = 0;
   $sum   = 0;

   $mth = array(0, 31,29,31,30,31,30,31,31,30,31,30,31);

   if ( (!is_array($dtm))          ||
        (!isset  ($dtm["month"])) ||
        (!isset  ($dtm["day"]))   ||
        (!isset  ($dtm["year"]))  ||
        ($dtm["month"] <  1)      ||
        ($dtm["month"] > 12)      ||
        ($dtm["day"]   <  1)      ||
        ($dtm["day"]   > 31)      ||
        ($dtm["year"]  < 1900)    ||
        ($dtm["year"]  > 2100) )
     {
       return FALSE;
     }

   $year =$dtm["year"];
   $month=$dtm["month"];
   $day  =$dtm["day"];

   $leapyear = isLeapYear($year);

   if ($month == 2)
     {
       if ($leapyear)
         { $dim=29; }
       else
         { $dim=28; }
     }
   else
     {
       $dim=$mth[$month];
     }

   $y = $year - 1900;
   if ($y > 0)
     {
       $lycount = (int) (($y-1) / 4);
     }
   $sum  = ($y - $lycount) * 365;
   $sum += ($lycount * 366);
   $juldays=$sum;
   for ($m=1; $m < $month; $m++)
     {
       if ($m == 2)  #- February
         {
           if ($leapyear)
             { $sum+=29; }
           else
             { $sum+=28; }
         }
       else
         { $sum += $mth[$m]; }
     }
   $sum  += $day;
   $juldays=$sum-$juldays;
                  //---  0=SUN  1=MON  2=TUE  3=WED  4=THU  5=FRI  6=SAT
   $dow   = (int)($sum % 7);
   $s1900 = $sum;

   $resp["DayOfWeek"]     = $dow;
   $resp["DaysInMonth"]   = $dim;
   $resp["DaysSince1900"] = $s1900;
   $resp["julian"]        = $juldays;
   return($resp);
 }


//-----------------------------------------------
// Subroutine: calReverse
//
// Given an int (days since 1900),
//           answer the gregorian date
//
// Args   :  int  (days since 1900)
// Returns Date Array (+ juldays)
//-----------------------------------------------
function calReverse($s1900)
 {
   $juldays   = 0;
   $savesum   = 0;
   $temp      = 0;
   $savedays  = 0;
   $savem     = 0;
   $saveyx    = 0;
   $m         = 0;
   $yx        = 0;
   $mth   = array(0, 31,29,31,30,31,30,31,31,30,31,30,31);

   for ($yx=1900;  $temp<$s1900;  $yx++)
      {
        $savesum  = $temp;
        $saveyx   = $yx;
        if (isLeapYear($yx))
          { $temp += 366; }
        else
          { $temp += 365; }
      }
    $juldays=(int) ($s1900-$savesum);

    $leapyear=isLeapYear($saveyx);
    $temp=0;
    for ($m=1; ($m<=12) && ($temp<$juldays); $m++)
      {
        $savedays = $temp;
        $savem=$m;
        if ($m == 2)
          {
            if ($leapyear)
              { $temp+=29; }
            else
              { $temp+=28; }
          }
        else
          { $temp += $mth[$m]; }
      }

    $dt["month"]   = $savem;
    $dt["day"]     = ($juldays-$savedays);
    $dt["year"]    = $saveyx;
    $dt["julian"]  = $juldays;
    return ($dt);
 }



//-----------------------------------------------
// Subroutine: calStepYears
//
// Given an int (+/- step value in YEARS) and a date,
//   answer the new date (Associative Array)
//
// Args   :  int  (+/- stepvalue in YEARS)
//           Reference Date (or DateTime) (associative array)
// Returns:  Date (or DateTime) (associative Array)
//-----------------------------------------------
function calStepYears($stepval, $dtm)
 {
   $mm = $dtm["month"];
   $dd = $dtm["day"];
   $yy = $dtm["year"];
   if (isset($dtm["hour"]))
     {
       return(getDateTimeArrayFromInts($mm,$dd,$yy+$stepval,$dtm['hour'],$dtm['minute'],$dtm['second']));
     }
   return(getDateArrayFromInts($mm,$dd,$yy+$stepval));
 }


//-----------------------------------------------
// Subroutine: calStepMonths
//
// Given an int (+/- step value in MONTHS) and a date,
//   answer the new date (Associative Array)
//
// Args   :  int  (+/- stepvalue in MONTHS)
//           Reference Date (or DateTime) (associative array)
// Returns:  Date (or DateTime) (associative Array)
//-----------------------------------------------
function calStepMonths($stepval, $dtm)
 {
   if ($stepval == 0)
     { return $dtm; }
   $temp = calendar($dtm);
   $s1900 = $temp["DaysSince1900"];

   $myy = (int) ($stepval / 12);
   if ($stepval < 0)
      { $stepval = -((-$stepval) % 12); }
   else
      { $stepval = $stepval % 12; }
   if ($myy == 0)
     {
       $dtm2=getDateArrayFromInts($dtm['month'],$dtm['day'],$dtm['year']);
     }
   else
     {
       $dtm2=calStepYears($myy, $dtm);
     }
   if ($stepval < 0)
     {
       $stepval = -$stepval;
       if ($stepval >= $dtm2['month'])
         {
           $stepval -= $dtm2['month'];
           $dtm2['year']--;
           $dtm2['month'] = 12;
         }
       $dtm2['month'] = $dtm2['month'] - $stepval;
     }
   else
     {
       if ($stepval > (12 - $dtm2['month']))
         {
           $stepval -= (12 - $dtm2['month']);
           $dtm2['year']++;
           $dtm2['month'] = 0;
         }
       $dtm2['month'] = $dtm2['month'] + $stepval;
     }

   if (isset($dtm['hour']))
     {
       $dtm2['hour']   = $dtm['hour'];
       $dtm2['minute'] = $dtm['minute'];
       $dtm2['second'] = $dtm['second'];
     }

   return($dtm2);
 }


//-----------------------------------------------
// Subroutine: calStepWeeks
//
// Given an int (+/- step value in WEEKS) and a date,
//   answer the new date (Associative Array)
//
// Args   :  int  (+/- stepvalue in WEEKS)
//           Reference Date (or DateTime) (associative array)
// Returns:  Date (or DateTime) (associative Array)
//-----------------------------------------------
function calStepWeeks($stepval, $dtm)
 {
   if ($stepval == 0)
     { return $dtm; }

   $temp = calendar($dtm);
   $s1900 = $temp["DaysSince1900"];
   $s1900 += (7*$stepval);
   $dtm2 = calReverse($s1900);
   if (isset($dtm['hour']))
     {
       $dtm2['hour']   = $dtm['hour'];
       $dtm2['minute'] = $dtm['minute'];
       $dtm2['second'] = $dtm['second'];
     }
   return($dtm2);
 }


//-----------------------------------------------
// Subroutine: calStepDays
//
// Given an int (+/- step value in DAYS) and a date,
//   answer the new date (Associative Array)
//
// Args   :  int  (+/- stepvalue in DAYS)
// Returns:  Date (associative Array)
//           Reference Date (or DateTime) (associative array)
//-----------------------------------------------
function calStepDays($stepval, $dtm)
 {
   if ($stepval == 0)
     { return $dtm; }

   $temp = calendar($dtm);
   $s1900 = $temp["DaysSince1900"];
   $s1900 += $stepval;
   $dtm2 = calReverse($s1900);
   if (isset($dtm['hour']))
     {
       $dtm2['hour']   = $dtm['hour'];
       $dtm2['minute'] = $dtm['minute'];
       $dtm2['second'] = $dtm['second'];
     }
   return($dtm2);
 }


//-----------------------------------------------
// Subroutine: dateDifference
//
// Given 2 dates (Associative Arrays), compute
//   the difference "in days" FROM DATE1 TO DATE2
//
// IF DATE2 is an OLDER DATE, the ANSWER IS NEGATIVE
// (if only the absolute difference is important)
// (simply negate any negative value returned  )
//
// Args   :  FROM (Reference) DATE  (Associative Array)
// Args   :  TO   (Target)    DATE  (Associative Array)
// Returns:  int  (days from DATE1 To DATE2) (0=Dates are equal)
//-----------------------------------------------
function dateDifference($dtm1, $dtm2)
 {
   $temp = calendar($dtm1);
   $r1900 = $temp["DaysSince1900"];
   $temp = calendar($dtm2);
   $t1900 = $temp["DaysSince1900"];
   $dif = $t1900 - $r1900;
   return($dif);
 }


//-----------------------------------------------
// Subroutine: dateDifferenceInMonths
//
// Given 2 dates (Associative Arrays), compute
//   the difference "in months" FROM DATE1 TO DATE2
//
// IF DATE2 is an OLDER DATE, the ANSWER IS NEGATIVE
// (if only the absolute difference is important)
// (simply negate anny negative value returned  )
//
// Args   :  FROM (Reference) DATE  (Associative Array)
// Args   :  TO   (Target)    DATE  (Associative Array)
// Returns:  int  (months from DATE1 To DATE2) (0=Dates are equal)
//-----------------------------------------------
function dateDifferenceInMonths($dtm1, $dtm2)
 {
   $dif=($dtm2['year'] - $dtm1['year']) * 12;
   $dif = $dif + ($dtm2['month'] - $dtm1['month']);
   return($dif);
 }


//-----------------------------------------------
// Subroutine: compareDates
//
// Given 2 dates (Associative Arrays), return
//      0  -  Date1 = Date 2
//     -1  -  Date1 < Date 2
//      1  -  Date1 > Date 2
//
// Args   :  Date1  (Associative Array)
// Args   :  Date2  (Associative Array)
// Returns:  int
//      0  -  Date1 = Date 2
//     -1  -  Date1 < Date 2
//      1  -  Date1 > Date 2
//-----------------------------------------------
function compareDates($dtm1, $dtm2)
 {
   $d1 = dateArrayToString($dtm1);
   $d2 = dateArrayToString($dtm2);
   return(strcmp($d1,$d2));
 }
?>
