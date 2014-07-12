<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type=text/javascript>
function signin(theForm)
 {
   alert("SigninId="+theForm.signin_id.value);
   window.top.hidePopWin();
 }
function signup(theForm)
 {
   window.top.hidePopWin();
   window.top.location.href="/signup.php";
 }
</script>
<title>Sign In</title>
<style>
body {
   background-color: #F7F3DF;
}

body, html, input {
   font-family:Verdana, Arial, Helvetica, sans-serif;
   font-size:12px;
   color: #222222;
}
</style>
</head>
<body>
    <form name=SIGNIN>
    <table align=center width="100%" cellpadding=0 cellspacing=3 border=0">
       <tr><td colspan=2 height=6 class=tinytext>&nbsp</td></tr>
       <tr>
          <td width="40%" align=right class=smalltext><b>Signin Id:</b>&nbsp;&nbsp;</td>
          <td width="60%" align=left  class=smalltext><input name=signin_id id=signin_id type=text size=20 maxlength=20 value=''>
       </tr>
       <tr>
          <td width="40%" align=right class=smalltext><b>Password:</b>&nbsp;&nbsp;</td>
          <td width="60%" align=left  class=smalltext><input name=password id=password type=text size=20 maxlength=20 value=''>
       </tr>
       <tr><td colspan=2 height=6 class=tinytext>&nbsp</td></tr>
       <tr><td align=center colspan=2 height=6 class=tinytext>
          <input type=button class=button value="  Sign In  " onclick="signin(this.form)">&nbsp;&nbsp;&nbsp;&nbsp;
          <input type=button class=button value="  Cancel  "  onclick="window.top.hidePopWin()">
       </tr>
       <tr><td colspan=2 height=26 class=tinytext>&nbsp</td></tr>
       <tr valign=middle>
          <td align=center colspan=2><b><span class="smalltext textmiddle">Not a member?</span>&nbsp; &nbsp;</b><input type=image src="/images/signup_here.png" onClick="signup()" style="padding:3px 0 3px 0; vertical-align:middle;"></td>
       </tr>
    </table>
    </form>
</body>
</html>
