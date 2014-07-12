function typeOf(value) {
    var s = typeof value;
    if (s === 'object') {
        if (value) {
            if (typeof value.length === 'number' &&
                    !(value.propertyIsEnumerable('length')) &&
                    typeof value.splice === 'function') {
                s = 'array';
            }
        } else {
            s = 'null';
        }
    }
    return s;
}


function isEmpty(o) {
    var i, v;
    if (typeOf(o) === 'object') {
        for (i in o) {
            v = o[i];
            if (v !== undefined && typeOf(v) !== 'function') {
                return false;
            }
        }
    }
    return true;
}

String.prototype.entityify = function () {
    return this.replace(/&/g, "&amp;").replace(/</g,        "&lt;").replace(/>/g, "&gt;");
};

String.prototype.quote = function () {
    var c, i, l = this.length, o = '"';
    for (i = 0; i < l; i += 1) {
        c = this.charAt(i);
        if (c >= ' ') {
            if (c === '\\' || c === '"') {
                o += '\\';
            }
            o += c;
        } else {
            switch (c) {
            case '\b':
                o += '\\b';
                break;
            case '\f':
                o += '\\f';
                break;
            case '\n':
                o += '\\n';
                break;
            case '\r':
                o += '\\r';
                break;
            case '\t':
                o += '\\t';
                break;
            default:
                c = c.charCodeAt();
                o += '\\u00' + Math.floor(c / 16).toString(16) +
                    (c % 16).toString(16);
            }
        }
    }
    return o + '"';
};

String.prototype.supplant = function (o) {
    return this.replace(/{([^{}]*)}/g,
        function (a, b) {
            var r = o[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        }
    );
};

String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
};


// The -is- object is used to identify the browser.  Every browser edition
// identifies itself, but there is no standard way of doing it, and some of
// the identification is deceptive. This is because the authors of web
// browsers are liars. For example, Microsoft's IE browsers claim to be
// Mozilla 4. Netscape 6 claims to be version 5.

var is = {
    ie:      navigator.appName == 'Microsoft Internet Explorer',
    java:    navigator.javaEnabled(),
    ns:      navigator.appName == 'Netscape',
    ua:      navigator.userAgent.toLowerCase(),
    version: parseFloat(navigator.appVersion.substr(21)) ||
             parseFloat(navigator.appVersion),
    win:     navigator.platform == 'Win32'
}
is.mac = is.ua.indexOf('mac') >= 0;
if (is.ua.indexOf('opera') >= 0) {
    is.ie = is.ns = false;
    is.opera = true;
}
if (is.ua.indexOf('gecko') >= 0) {
    is.ie = is.ns = false;
    is.gecko = true;
}


function callbackHandler(xhrRequest, result, cb) {
  var headers         = xhrRequest.getAllResponseHeaders();
  var contentType     = xhrRequest.getResponseHeader("Content-Type");
  var responseText    = xhrRequest.responseText;
  var responseXML     = null;
  try {
    if (!isEmpty(xhrRequest.responseXML))
       responseXML=xhrRequest.responseXML;
  } catch(e) { /** e.show(); **/ }
  var status       = xhrRequest.status;
  var statusText   = xhrRequest.statusText;

  var theaders = headers.split("\n");
  var responseHeaders = {};
  for (var i=0; i < theaders.length; i++) {
      var pos = theaders[i].indexOf(":");
      if (pos > 0) {
        var k=theaders[i].substring(0,pos);
        var v=theaders[i].substring(pos+1).trim();
        responseHeaders[k]=v;
      }
  }

  result.contentType       = contentType;
  // result.headers           = headers;
  result.responseHeaders   = responseHeaders;
  result.responseText      = responseText;
  result.responseData      = JSON.parse(responseText);
  result.responseXML       = responseXML;
  result.status            = status;
  result.statusText        = statusText;

  // alert("Complete: " +status+" "+statusText+"\nResult: \n"+objectToString(result)+"\n");
  if (typeof cb === 'function') {
    cb (xhrRequest, result);
  }
}


Error.prototype.show = function(message) {
  var m="";
  if (arguments.length >= 1 && typeof message == "string" && message.length > 0)
    m=message;
  var t="";
  if (m.length > 0)
     t += "----------- Exception Occurred:  "+m+" ----------- \n \n";
  else
     t += "----------- Exception Occurred ----------- \n \n";
  t += "File:Line      :  "+this.fileName    +"  ("+this.lineNumber+") \n";
  t += "Message     :  "+this.message     +" \n";
  t += "Name/Type :  "+this.name        +" \n";
  t += "Stack:\n";
  if (typeof this.stack == "string" && this.stack.length > 0)
    {
      var s   = this.stack.replace("\r", "");
      var stk = s.split("\n");
      for (var j=0; j<stk.length; j++) {
          if (stk[j].length > 0) {
             if (j==0)
               t += "   >>>  ";
             else
               t += "   from: ";
             t += stk[j]+"\n";
          }
      }
    }

  alert(t);
  return t;
}


onerror=errorHandler;

function errorHandler(msg,url,line) {
   var t = "----------- Error Occurred -----------         \n";
   t+="     Error: " + msg + "\n";
   t+="     URL  : " + url + "\n";
   if (is.ie && (typeOf(line) == "number"))
     line--;
   t+="     Line : " + line + "\n\n";
   alert(t);
   return true;
}
