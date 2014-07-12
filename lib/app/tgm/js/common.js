var reviewWindow;
var page_attempted="";

function linkToTag(tagname)
 {
   window.location.href=tagname;
 }

function linkToPage(url)
 {
   top.location.href=url;
 }


var dialogInfo=null;
function dialog_response()
  {
    var info=dialogInfo;
    dialogInfo=null;

    if (info==null)
      {
        alert("Dialog Response");
      }
    else
      {
        if (info.callback != null)
          {
            info.callback(info);
          }
      }
  }


function findPos(obj)
 {
   var curleft = curtop = 0;
   if (obj.offsetParent) {
     do {
          curleft += obj.offsetLeft;
          curtop += obj.offsetTop;
        } while (obj = obj.offsetParent);
     return [curleft,curtop];
   }
 }

function generic_hide(id)
 {
   var el = document.getElementById(id);
   if (el) el.style.display='none';
 }

function generic_show(id)
 {
   var el = document.getElementById(id);
   if (el) el.style.display='';
 }

function generic_toggle(id)
 {
   var el = document.getElementById(id);
   if (el)
     {
       if (el.style.display=='none')
          el.style.display='';
       else
          el.style.display='none';
     }
 }


function searchProduct(stype, dwidth, dheight, callback)
  {
    if (arguments.length < 4) callback=null;
    dialogInfo = {
                   stype:         stype,
                   callback:      callback
                 }
    var url = "/search_product_dialog.php?stype="+stype;
    showPopWin('Search '+stype, url, dwidth, dheight, dialog_response);
  }


function searchProductCategory(url, dwidth, dheight, callback)
  {
     if (arguments.length < 4) callback=null;
     dialogInfo = {
                    callback:      callback
                  }
    showPopWin('Search', url, dwidth, dheight, dialog_response);
  }


function searchArticles(url, dwidth, dheight, callback)
  {
     if (arguments.length < 4) callback=null;
     dialogInfo = {
                    callback:      callback
                  }
    showPopWin('Search', url, dwidth, dheight, dialog_response);
  }


function systemDialog(url, dwidth, dheight, callback)
  {
     if (arguments.length < 4) callback=null;
     dialogInfo = {
                    callback:      callback
                  }
    showPopWin('Special Notice', url, dwidth, dheight, dialog_response);
  }


function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
       func();
    }
  }
}


function WindowOnload(f)
 {
   var prev=window.onload;
   window.onload=function(){ if(prev)prev(); f(); }
 }


function setOpacity(obj,value)
 {
    obj.style.opacity = value/10;
    obj.style.filter = 'alpha(opacity=' + value*10 + ')';
 }


function findPosX(obj)
  {
    var curleft = 0;
    if (obj.offsetParent)
      {
        while (1)
          {
            curleft+=obj.offsetLeft;
            if (!obj.offsetParent)
              {
                break;
              }
            obj=obj.offsetParent;
          }
      }
    else
    if (obj.x)
      {
        curleft+=obj.x;
      }
    return curleft;
  }


function findPosY(obj)
  {
    var curtop = 0;
    if (obj.offsetParent)
      {
        while (1)
          {
            curtop+=obj.offsetTop;
            if (!obj.offsetParent)
              {
                break;
              }
            obj=obj.offsetParent;
          }
      }
    else
    if (obj.y)
      {
        curtop+=obj.y;
      }
    return curtop;
  }



function onResponse(returnVal) {
  if (returnVal)
    {
//    var msg  = "Function Name : "+returnVal.functionName+"\n";
//        msg += "Signin ID     : "+returnVal.signin_id+"\n";
//        msg += "Password      : "+returnVal.password+"\n";
//    alert(msg);
    }
  else
    {
      // alert("onResponse - No Values");
    }
}



// --- Set_Cookie ---------------------------------------------------------------------
// You need to put the name and values in quotes when you call the function, like this:
//
// Set_Cookie( 'mycookie', 'visited 9 times', 30, '/', '', '' );
//
// Don't forget to put in empty quotes for the unused parameters or you'll get an error.
// This example makes the cookie named 'mycookie', with the value of 'visited 9 times',
// and with a life of 30 days, and the cookie is set to your root folder.
//-------------------------------------------------------------------------------------
function Set_Cookie( name, value, expires, path, domain, secure )
 {
   // set time, it's in milliseconds
   var today = new Date();
   today.setTime( today.getTime() );

   /* if the expires variable is set, make the correct  */
   /* expires time, the current script below will set   */
   /* it for x number of days, to make it for hours,    */
   /* delete * 24, for minutes, delete * 60 * 24        */

   if ( expires )
    {
      expires = expires * 1000 * 60 * 60 * 24;      /* expires specified in days     */
      //  expires = expires * 1000 * 60 * 60;       /* expires specified in hours    */
      //  expires = expires * 1000 * 60;            /* expires specified in minutes  */
    }
   var expires_date = new Date( today.getTime() + (expires) );

   document.cookie = name + "=" +escape( value ) +
      ( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
      ( ( path ) ? ";path=" + path : "" ) +
      ( ( domain ) ? ";domain=" + domain : "" ) +
      ( ( secure ) ? ";secure" : "" );
 }


// --- Get_Cookie ---------------------------------------------------------------------
function Get_Cookie( check_name )
 {
    // first we'll split this cookie up into name/value pairs
    // note: document.cookie only returns name=value, not the other components
    var a_all_cookies = document.cookie.split( ';' );
    var a_temp_cookie = '';
    var cookie_name = '';
    var cookie_value = '';
    var b_cookie_found = false; // set boolean t/f default f

    for ( i = 0; i < a_all_cookies.length; i++ )
      {
        // now we'll split apart each name=value pair
        a_temp_cookie = a_all_cookies[i].split( '=' );

        // and trim left/right whitespace while we're at it
        cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

        // if the extracted name matches passed check_name
        if ( cookie_name == check_name )
          {
            b_cookie_found = true;
            // we need to handle case where cookie has no value but exists (no = sign, that is):
            if ( a_temp_cookie.length > 1 )
            {
                cookie_value = unescape( a_temp_cookie[1].replace(/^\s+|\s+$/g, '') );
            }
            // note that in cases where cookie is initialized but no value, null is returned
            return cookie_value;
            break;
          }
        a_temp_cookie = null;
        cookie_name = '';
      }
    if ( !b_cookie_found )
      {
        return null;
      }
 }


// this deletes the cookie when called
function Delete_Cookie( name, path, domain)
 {
   if (Get_Cookie(name))
     {
        document.cookie = name + "=" +
               ( ( path ) ? ";path=" + path : "") +
               ( ( domain ) ? ";domain=" + domain : "" ) +
               ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
     }
 }


function trim(stringToTrim) {
    return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function ltrim(stringToTrim) {
    return stringToTrim.replace(/^\s+/,"");
}
function rtrim(stringToTrim) {
    return stringToTrim.replace(/\s+$/,"");
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


function stripWhiteSpace(aString)
{
  var newString = "";
  for (i=0; i<aString.length; i++)
    {
      if (aString.charAt(i) != ' '  &&
          aString.charAt(i) != '\r' &&
          aString.charAt(i) != '\n' &&
          aString.charAt(i) != '\t')
        {
          newString=newString+aString.charAt(i);
        }
    }
  return(newString);
}

function startsWith(source, aString, case_sensitive)
  {
    var src;
    var str;
    if (case_sensitive)
      {
        src = source;
        str = aString;
      }
    else
      {
        src = source.toLowerCase();
        str = aString.toLowerCase();
      }
    if (str.length > src.length)
      return false;
    if (str == src)
      return true;
    if (src.substring(0,str.length) == str)
      return true;
    return false;
  }


function endsWith(source, aString, case_sensitive)
  {
    var src;
    var str;
    if (case_sensitive)
      {
        src = source;
        str = aString;
      }
    else
      {
        src = source.toLowerCase();
        str = aString.toLowerCase();
      }
    if (str.length > src.length)
      return false;
    if (str == src)
      return true;
    var pos = src.length - str.length;
    if (src.substring(pos) == str)
      return true;
    return false;
  }


function padTo(s,len)
  {
    var temp=s;
    if (s.length < len)
      {
        var n = len - s.length;
        for (var i=0; i<n; i++)
         temp=temp+" ";
      }
    return temp;
  }


function countDigits(aString)
{
  var count=0;
  for (i=0; i<aString.length; i++)
    {
      if (aString.charAt(i) >= '0' && aString.charAt(i) <= '9')
        {
          count++;
        }
    }
  return(count);
}


function ucfirst(str,lowercase_remaining)
  {
    var s="";
    if (str.length > 0)
      {
        var res="";
        var fc = str.substring(0,1);
        if (str.length > 1)
          res=str.substring(1);

        if (lowercase_remaining)
          s = fc.toUpperCase() + res.toLowerCase();
        else
          s = fc.toUpperCase() + res;
      }
    return s;
  }


function getDigits(aString)
{
  var newString = "";
  for (i=0; i<aString.length; i++)
    {
      if (aString.charAt(i) >= '0' && aString.charAt(i) <= '9')
        {
          newString=newString+aString.charAt(i);
        }
    }
  return(newString);
}

function isAlphabetic(aString)
{
  for (i=0; i<aString.length; i++)
    {
      if (! ((aString.charAt(i) >= 'a' && aString.charAt(i) <= 'z') ||
             (aString.charAt(i) >= 'A' && aString.charAt(i) <= 'Z')) )
        return false;
    }
  return true;
}

function isAlphanumeric(aString, alsoAllowed)
{
  if (arguments.length == 1)
    alsoAllowed="";
  for (i=0; i<aString.length; i++)
    {
      if (! ((aString.charAt(i) >= 'a' && aString.charAt(i) <= 'z') ||
             (aString.charAt(i) >= 'A' && aString.charAt(i) <= 'Z') ||
             (aString.charAt(i) >= '0' && aString.charAt(i) <= '9')) )
        if (alsoAllowed.indexOf(aString.charAt(i)) == -1)
          return false;
    }
  return true;
}

function isNumeric(aString)
{
  for (i=0; i<aString.length; i++)
    {
      if (! (aString.charAt(i) >= '0' && aString.charAt(i) <= '9') )
        return false;
    }
  return true;
}

function getNumber(aString)
 {
   var num=0;
   var newString = "";
   for (i=0; i<aString.length; i++)
     {
       if (aString.charAt(i) >= '0' && aString.charAt(i) <= '9')
         {
           newString=newString+aString.charAt(i);
         }
       else
         break;
     }

   for (i=newString.length-1, j=1; i>=0; i--, j*=10)
     {
       num += (j * asNumber(newString.charAt(i)));
     }
   return(num);
 }

function asNumber(aChar)
 {
   return(aChar - '0');
 }


function isValidEmail(email)
  {
    return verify_email(email);
  }

function isValidEmailAddress(email)
  {
    return verify_email(email);
  }

function isValidURL(url)
  {
    if (url.length < 7 || url.substring(0,7) != "http://")
      return false;
    return true;
  }

function isValidPW(aString)
  {
    if (aString.length <  6) return false;
    if (aString.length > 20) return false;
    var digits=0;
    for (i=0; i<aString.length; i++)
      {
        if (! ((aString.charAt(i) >= 'a' && aString.charAt(i) <= 'z') ||
               (aString.charAt(i) >= 'A' && aString.charAt(i) <= 'Z') ||
               (aString.charAt(i) >= '0' && aString.charAt(i) <= '9')) )
          return false;
        if (aString.charAt(i) >= '0' && aString.charAt(i) <= '9') digits++;
      }
    if (digits < 1)  return false;
    return true;
  }

function verify_email(email)
 {
   var reg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/;                               // not valid
   var reg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/;  // valid
   if (!reg1.test(email) && reg2.test(email))
     {
       return true;
     }
   return false;
 }


function ShowFormVariables(theForm)
  {
    msg="";
    for (var i=0; i<theForm.elements.length; i++)
      {
        msg += padTo("NAME: "+theForm.elements[i].name,  30);
        msg += padTo("TYPE: "+theForm.elements[i].type,  30);
        msg += "VALUE: "+theForm.elements[i].value;
        msg += "TEXT: "+theForm.elements[i].text;
        msg += "\n";
      }
    alert(msg);
    return;
  }


function launchPopup(url,pagetitle,args)
  {
    var winHandle = top.open(url,pagetitle,args);
    if (winHandle != null)
      {
        if (winHandle.opener == null) winHandle.opener = self;
      }
    return winHandle;
  }


function clink(url)
  {
    var wWidth  = 780;
    var wHeight = 750;

    var topmargin  = 0;
    var leftmargin = 0;

    var win=launchPopup(url,"PushyaAds",
       'width='+wWidth+',height='+wHeight+',top='+topmargin+',left='+leftmargin+
       ',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes');
//  if (win.opener == null) win.opener = self;
  }

function openPopupWindow(url,top,left,width,height)
  {
    var winHandle=launchPopup(url,"PushyAds",
       'width='+width+',height='+height+',top='+top+',left='+left+
       ',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes');
//  if (winHandle.opener == null) winHandle.opener = self;
  }

function openPopup(url,width,height,sb,resize)
  {
    var top=0;
    var left=0;
    if (width  < 50) width=50;
    if (height < 50) height=50;
    var scroll="yes";
    var resizable="no";
    if (arguments.length>=4 && (!sb))
      scroll="no";
    if (arguments.length>=5 && (resize))
      resizable="yes";

    // alert("scroll="+scroll);

    var winHandle=launchPopup(url,"PushyAds",
       'width='+width+',height='+height+',top='+top+',left='+left+
       ',scrollbars='+scroll+',location=no,directories=no,status=no,menubar=no,toolbar=no,resizable='+resizable);
//  if (winHandle.opener == null) winHandle.opener = self;
  }


function upgradeCallback(info)
  {
    // alert("UpgradeCallback");
  }


function fOpenWindow(url)
  {
    var wWidth  = 800;
    var wHeight = 760;

    var topmargin  = 0;
    var leftmargin = 0;

    var win=launchPopup(url,"PushyAds",
       'width='+wWidth+',height='+wHeight+',top='+topmargin+',left='+leftmargin+
       ',scrollbars=yes,location=no,directories=no,status=no,menubar=no,toolbar=no,resizable=yes');
//  if (win.opener == null) win.opener = self;
  }

function toggle(id)
  {
    var hidden=isElementHidden(id);
    if (hidden)
      showElement(id);
    else
      hideElement(id);
  }

function isElementHidden(id)
  {
    var el=document.getElementById(id);
    if ((el) && (el.style.display=="none"))
      {
        return true;
      }
    return false;
  }

function showElement(id)
  {
    var el=document.getElementById(id);
    if (el)
      {
        el.style.display="";              // SHOW
      }
  }

function hideElement(id)
  {
    var el=document.getElementById(id);
    if (el)
      {
        el.style.display="none";          // HIDE
      }
  }


function rand(from,to)
 {
   //-------------------------
   // var n=rand(20,80);  (INCLUSIVE)
   //-------------------------
   var n = Math.floor((to-(from-1))*Math.random()) + from;
   return(n);
 }


function getUniqueWindowName(prefix)
  {
    var d  = new Date();
    var tm = d.getTime();
    var s  = prefix+"_"+tm;
    return s;
  }


function urlEncode(string)
 {
   return escape(_utf8_encode(string));
 }


function urlDecode(string)
 {
   return _utf8_decode(unescape(string));
 }


function _utf8_encode(string)
 {
   string = string.replace(/\r\n/g,"\n");
   var utftext = "";

   for (var n = 0; n < string.length; n++) {

       var c = string.charCodeAt(n);

       if (c < 128) {
           utftext += String.fromCharCode(c);
       }
       else if((c > 127) && (c < 2048)) {
           utftext += String.fromCharCode((c >> 6) | 192);
           utftext += String.fromCharCode((c & 63) | 128);
       }
       else {
           utftext += String.fromCharCode((c >> 12) | 224);
           utftext += String.fromCharCode(((c >> 6) & 63) | 128);
           utftext += String.fromCharCode((c & 63) | 128);
       }

   }

   return utftext;
 };


function _utf8_decode(utftext)
 {
   var string = "";
   var i = 0;
   var c = c1 = c2 = 0;

   while ( i < utftext.length )
     {
       c = utftext.charCodeAt(i);

       if (c < 128) {
           string += String.fromCharCode(c);
           i++;
       }
       else if((c > 191) && (c < 224)) {
           c2 = utftext.charCodeAt(i+1);
           string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
           i += 2;
       }
       else {
           c2 = utftext.charCodeAt(i+1);
           c3 = utftext.charCodeAt(i+2);
           string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
           i += 3;
       }
     }
   return (string);
 }
