
<?php
 require("pushy_common.inc");

 $now=time();
 $dateTimeNow=formatDateTime($now,TRUE);


 $date="";
 $time="";
 if (isset($_REQUEST["mstime"]) && (strlen($_REQUEST["mstime"]) > 0))
   {
     $date=formatDate((int) $_REQUEST["mstime"]);
     $time=formatTime((int) $_REQUEST["mstime"], TRUE);
   }
 if (isset($_REQUEST["datetime"]) && (strlen($_REQUEST["datetime"]) > 0))
   {
     $datetimeArray = dateTimeToArray($_REQUEST["datetime"]);
     $timeinseconds = timestampFromDateTimeArray($datetimeArray);
   }
?>

<html>

<head>
<script LANGUAGE="JavaScript">
<!--
 function ValidateForm(theForm)
  {
    theForm.mstime.value = stripa(theForm.mstime.value);
    if (theForm.mstime.value == "" || theForm.mstime.value == " ")
     {
       alert("Please enter a TIME() in seconds.");
       theForm.mstime.focus();
       return (false);
     }
     return (true);
   }

 function ValidateForm2(theForm)
  {
    theForm.datetime.value = striplt(theForm.datetime.value);
    if (theForm.datetime.value == "" || theForm.datetime.value == " ")
     {
       alert("Please enter a Date Time.");
       theForm.datetime.focus();
       return (false);
     }
     return (true);
   }

 function stripa(aString)
   {
     var newString = "";
     for (i=0; i<aString.length; i++)
       {
         if (aString.charAt(i) != ' ')
           {
             newString=newString+aString.charAt(i);
           }
       }
     return(newString);
   }

 function stripl(aString)
   {
     var newString = "";
     for (i=0; i<aString.length; i++)
       {
         if (aString.charAt(i) != ' ')
           {
             for (j=i; j<aString.length; j++)
               newString=newString+aString.charAt(j);
             return(newString);
           }
       }
     return(newString);
   }

 function stript(aString)
   {
     var newString = "";
     var len = aString.length;
     for (i=aString.length-1; i>=0; i--)
       {
         if (aString.charAt(i) == ' ')
           len--;
         else
           {
             for (j=0; j<len; j++)
               newString=newString+aString.charAt(j);
             return(newString);
           }
       }
     return(newString);
   }

 function striplt(aString)
   {
     var newString;
     newString = stripl(aString);
     newString = stript(newString);
     return(newString);
   }
//-->
</script>

<title>Time</title>
</head>

<div align="center"><center>

<table border="0" cellpadding="0" cellspacing="0" width="90%">
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%"><font face="Arial"><font color="#0000A0">DateTime Now:&nbsp;</font>(<?php echo $now?>)&nbsp;&nbsp;&nbsp;<?php echo $dateTimeNow?></td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%"><font face="Arial"><font color="#CC0000"><small><strong>Date Time from Seconds</strong></small></td>
  </tr>
  <tr>
    <td width="90%">
      <form method="POST" action="index.php" onsubmit="return ValidateForm(this)">
      <div align="center"><center>
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td width="30%" align="right"><strong><font face="Arial" color="#0000A0">
            Time - in seconds:&nbsp;&nbsp;</font></strong></td>
          <td width="70%"><input type="text" name="mstime" size="20" tabindex="1" value="<?php echo $mstime?>"></td>
        </tr>
        <!-- tr>
          <td width="30%" align="right"><strong><font face="Arial" color="#0000A0">
            Date:&nbsp;&nbsp;</font></strong></td>
          <td width="70%" align="left"><font face="Arial"><?php echo $date?></font></td>
        </tr>
        <tr>
          <td width="30%" align="right"><strong><font face="Arial" color="#0000A0">
            Time:&nbsp;&nbsp;</font></strong></td>
          <td width="70%" align="left"><font face="Arial"><?php echo $time?></font></td>
        </tr -->
        <tr>
          <td width="30%" align="right"><strong><font face="Arial" color="#0000A0">
            Date Time:&nbsp;&nbsp;</font></strong></td>
          <td width="70%" align="left"><font face="Arial"><?php echo $date." ".$time?></font></td>
        </tr>
      </table>
      </center></div>
      <div align="left"><p>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value=" Convert "
           name="Submit" tabindex="2"></p>
      </div>
    </form>
    </td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%"><font face="Arial"><font color="#CC0000"><small><strong>Seconds from Date Time</strong></small></td>
  </tr>
  <tr>
    <td width="90%">
      <form method="POST" action="index.php" onsubmit="return ValidateForm2(this)">
      <div align="center"><center>
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td width="30%" align="right"><strong><font face="Arial" color="#0000A0">
            Date Time:&nbsp;&nbsp;</font></strong></td>
          <td width="70%"><input type="text" name="datetime" size="20" tabindex="1" value="<?php echo $datetime?>"></td>
        </tr>
        <tr>
          <td width="30%" align="right"><strong><font face="Arial" color="#0000A0">
            Time - in seconds:&nbsp;&nbsp;</font></strong></td>
          <td width="70%" align="left"><font face="Arial"><?php echo $timeinseconds?></font></td>
        </tr>
      </table>
      </center></div>
      <div align="left"><p>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value=" Convert "
           name="Submit" tabindex="2"></p>
      </div>
    </form>
    </td>
  </tr>
</table>
</center></div>
</body>
</html>
