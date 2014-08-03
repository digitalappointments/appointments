<?php
include_once("tgm_common.inc");
include_once("tgm_commonsql.inc");
include_once("tgm.inc");
include_once("tgm_jsontools.inc");

$db=getDatabaseConnection();

if (isset($_REQUEST["mid"]))
  {
    if (isset($_REQUEST["mid"]) && is_array($memberRecord=getMemberInfo($db,$_REQUEST["mid"])))
      {
        $_firstname_ = stripslashes($memberRecord["firstname"]);
        $_lastname_  = stripslashes($memberRecord["lastname"]);
        $_email_     = stripslashes($memberRecord["email"]);
      }
  }
?>
<html>
<head>

<LINK type="text/css" rel="stylesheet" href="/css/styles.css">
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/js/jsutils.js"></script>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.json-2.2.min.js"></script>

<LINK type=text/css rel=stylesheet href="/js/modal/subModal.css">
<script type="text/javascript" src="/js/modal/subModal.js"></script>

<script type="text/javascript">
function dialog_register_submit(theForm)
 {
   if (!dialog_registerValidation(theForm))
     return;

   var formName = theForm.name;

   var data={
               firstname:  theForm.firstname.value,
               lastname:   theForm.lastname.value,
               email:      theForm.email.value.toLowerCase()
            }
//   var_dump(data);
//   return;

   $.ajax({
      type:     "POST",
      url:      "/register.php",
      data:     data,
      cache:    false,
      dataType: "json",
      error:    function (XMLHttpRequest, textStatus, errorThrown)
                {
                  var httpStatus=XMLHttpRequest.status;
                  alert("Request Failed - HTTP Status:"+ httpStatus+" : "+textStatus);
                },
      success:  function(response, textStatus)
                {
                  var status=response.status;
                    // alert("Status="+status);
                    // alert(objectToString(response));
                  if (status != 0)
                    {
                      if (status==211) // ALREADY REGISTERED
                        {
                          var id = formName+"_ALREADY_REGISTERED";
                          var el = document.getElementById(id);
                          el.style.display = '';
                        }
                      else
                        alert( response.message );
                    }
                  else
                    {
                      // alert(objectToString(response));
                      var data=response.data;
                      // alert("Redirecting to("+data.url+")");
                      window.top.hidePopWin(false);
                      top.location.href=data.url;
                    }
                }
   });
 }


function dialog_registerValidation(theForm)
  {
    theForm.firstname.value = theForm.firstname.value.trim();
    theForm.lastname.value  = theForm.lastname.value.trim();
    theForm.email.value     = theForm.email.value.trim();

    if (theForm.firstname.value.length == 0)
      {
        alert("Please enter your First Name");
        theForm.firstname.focus();
        return (false);
      }
    if (theForm.lastname.value.length == 0)
      {
        alert("Please enter your Last Name");
        theForm.lastname.focus();
        return (false);
      }
    if (theForm.email.value.length == 0)
      {
        alert("Please enter your Email address");
        theForm.email.focus();
        return (false);
      }
    if ((!isValidEmailAddress(theForm.email.value)))
      {
        alert("Email Address invalid: Please Re-enter your Email Address.");
        theForm.email.focus();
        return (false);
      }

    return true;
  }


function dialog_already_registered_login()
  {
    window.top.hidePopWin(false);
    parent.already_registered_login();
  }

function dialog_already_registered_forgot_password()
  {
    window.top.hidePopWin(false);
    parent.already_registered_forgot_password();
  }

</script>
</head>
<body>
  <!--------------------------------------------- START SIGNUP FORM --------------------------------------------->
  <table align=center width=470 cellspacing=3 cellpadding=3 class=bgborder bgcolor="#FFC757" style="margin-top: 3px;">
    <tr>
      <td colspan=3 style="border: 0px; background-color:#FFC757;">
        <form id="DIALOG_SIGNUP_FORM" name="DIALOG_SIGNUP_FORM" method="POST">
        <table style="border:1px solid #999999;" width=100% cellpadding=0 cellspacing=0>
          <tr bgcolor="#F7F7F7">
            <td colspan=2 height=40></td>
          </tr>

          <tr height=28>
            <td width=45% class="signcol1 tahoma">
               <b>Your Firstname:&nbsp;</b></td>
            <td width=55% class=signcol2>&nbsp;
               <input class=input type="text" name="firstname" maxlength="30" value="<?php echo $_firstname_?>">
            </td>
          </tr>

          <tr height=28>
            <td width=45% class="signcol1 tahoma">
               <b>Your Lastname:&nbsp;</b></td>
            <td width=55% class=signcol2>&nbsp;
              <input class=input type="text" name="lastname" maxlength="30"   value="<?php echo $_lastname_?>">
            </td>
          </tr>

          <tr height=28>
            <td width=45% class="signcol1 tahoma">
               <b>Your Email:&nbsp;</b></td>
            <td width=55% class=signcol2>&nbsp;
               <input class=input type="text" name="email" maxlength="70"     value="<?php echo $_email_?>">
            </td>
          </tr>

          <tr bgcolor="#F7F7F7">
            <td colspan=2 height=20>
               <div id="DIALOG_SIGNUP_FORM_ALREADY_REGISTERED" style="display: none; margin:15px 0px;  text-align:center" align=center>
                  <span style="color:#CC0000;" class="tahoma size12">
                    The email address you entered has already been registered<br>
                    <a href=javascript:dialog_already_registered_login()>LOGIN HERE</a>
                    ...or, if you forgot your password <a href=javascript:dialog_already_registered_forgot_password()>CLICK HERE</a>
                  </span>
               </div>
            </td>
          </tr>
          <tr valign=center height=54 bgcolor="#F7F7F7" cellpadding=0 cellspacing=0 >
            <td colspan=2 width="100%" align=center style="padding-left:10px;"><a href="#" onClick="return false" style="cursor:hand;">
              <img src="/images/btn_signup.png" border=0 width=100 onClick=javascript:dialog_register_submit(document.DIALOG_SIGNUP_FORM)></a>
            </td>
          </tr>

          <tr bgcolor="#F2F4F7">
            <td colspan=2 height=20></td>
          </tr>
        </table>
        </form>
      </td>
    </tr>
  </table>
  <div align=center><img src="http://pds1106.s3.amazonaws.com/images/shadow.gif" width=430 height=31></div>
  <!--------------------------------------------- END SIGNUP FORM ----------------------------------->
</body>
</html>
