function typeOf(value) {
    try {
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
    } catch (e) {
       return "undefined";
    }
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


function findPosition(obj)
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

String.prototype.startsWith = function(s) {
    if (s.length <= this.length &&
       ((s==this) || (this.substring(0,s.length) == s)) )
      return true;
    return false;
}

String.prototype.endsWith = function(s) {
    if (s.length <= this.length &&
       ((s==this) || (this.substring(this.length - s.length) == s)) )
      return true;
    return false;
}


String.prototype.padTo = function(len) {
    var temp=this;
    if (this.length < len)
      {
        var n = len - this.length;
        for (var i=0; i<n; i++)
         temp+=" ";
      }
    return temp;
}


function padLevel(level)
 {
   var t='';
   for (var i=0; i<level; i++) t+='    ';
   return t;
 }

function objectToString(o)
 {
   var lvl=1;
   if (arguments.length >= 2)
     lvl=arguments[arguments.length-1];
   var text='';
   if (typeOf(o) === 'object' && !isEmpty(o)) {
      var k,v;
      for (k in o) {
        v = o[k];
        if (v !== undefined && typeOf(v) !== 'function') {
          if (typeOf(v) === 'object' && !isEmpty(v)) {
            text+=padLevel(lvl)+k+':\n';
            text+=objectToString(v,lvl+1);
          }
          else {
            text+=padLevel(lvl)+k+': '+v+'\n';
          }
        }
      }
   }
   return text;
 }


function var_dump(o) { dumpObject(o); }

function dumpObject(o) {
   if (arguments.length >= 1 && (typeOf(o) == "object" || typeOf(o) == "function"))
     {
        alert(objectToString(o));
     }
}

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


Error.prototype.getMessage = function(messageTitle) {
  var m="";
  if (arguments.length >= 1 && typeof messageTitle == "string" && messageTitle.length > 0)
    m=messageTitle;
  var t="";
  if (m.length > 0)
     t += "----------- Exception Occurred:  "+m+" ----------- \n \n";
  else
     t += "----------- Exception Occurred ----------- \n \n";
  var ln = this.lineNumber;
  // if (is.ie && (typeOf(this.lineNumber) == "number"))
  //   ln--;
  t += "File:Line      :  "+this.fileName    +"  ("+ln+") \n";
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
  // alert(t);
  return t;
}


Error.prototype.show = function(messageTitle) {
  var t = this.getMessage(messageTitle);
  alert(t);
  return;
}

onerror=errorHandler;

function errorHandler(msg,url,line) {
   var t = "----------- Error Occurred -----------         \n";
   t+="     Error: " + msg + "\n";
   t+="     URL  : " + url + "\n";
// if (is.ie && (typeOf(line) == "number"))
//   line--;
   t+="     Line : " + line + "\n\n";
   alert(t);
   return t;
   // return true;
}


function AjaxGet(url) {
   var AJAX;
   if (window.XMLHttpRequest) {
      AJAX=new XMLHttpRequest();
   } else {
      AJAX=new ActiveXObject("Microsoft.XMLHTTP");
   }
   if (AJAX) {
     AJAX.open("GET", url, false);
     AJAX.send(null);
     if (AJAX.status >= 300)
       return false;
     return AJAX.responseText;
   } else {
     return false;
   }
}

function AjaxPost(url, data) {
  var AJAX;
  if (window.XMLHttpRequest) {
     AJAX=new XMLHttpRequest();
  } else {
     AJAX=new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (AJAX) {
    AJAX.open("POST", url, false);
    AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    AJAX.send(data);
    return AJAX.responseText;
  } else {
     return false;
  }
}


function AjaxSyncGet(url) {
   var AJAX;
   if (window.XMLHttpRequest) {
      AJAX=new XMLHttpRequest();
   } else {
      AJAX=new ActiveXObject("Microsoft.XMLHTTP");
   }
   if (AJAX) {
     AJAX.open("GET", url, false);
     AJAX.send(null);
     if (AJAX.status >= 300)
       return false;
     return AJAX.responseText;
   } else {
     return false;
   }
}

function AjaxSyncPost(url, data) {
  var AJAX;
  if (window.XMLHttpRequest) {
     AJAX=new XMLHttpRequest();
  } else {
     AJAX=new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (AJAX) {
    AJAX.open("POST", url, false);
    AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    AJAX.send(data);
    return AJAX.responseText;
  } else {
     return false;
  }
}


function absValue(n)
  {
    if (n>=0)
      return n;
    return (-n);
  }

function pageWidth()
 {
   return window.innerWidth != null? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body != null ? document.body.clientWidth : null;
 }
function pageHeight()
 {
   return  window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ?  document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;
 }
function posLeft()
 {
   return typeof window.pageXOffset != 'undefined' ? window.pageXOffset :document.documentElement && document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft ? document.body.scrollLeft : 0;
 }
function posTop()
 {
   return typeof window.pageYOffset != 'undefined' ?  window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ? document.body.scrollTop : 0;
 }
function posRight()
 {
   return posLeft()+pageWidth();
 }
function posBottom()
 {
   return posTop()+pageHeight();
 }


function scrollWidth()
{
  if (window.innerHeight && window.scrollMaxY) {    // Firefox
    return window.innerWidth + window.scrollMaxX;
  }
  if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
    return document.body.scrollWidth;
  }
  else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari
    return document.body.offsetWidth;
  }
}

function scrollHeight()
{
  if (window.innerHeight && window.scrollMaxY) {    // Firefox
    return window.innerHeight + window.scrollMaxY;
  }
  if (document.body.scrollHeight > document.body.offsetHeight) { // all but Explorer Mac
    return document.body.scrollHeight;
  }
  else { // works in Explorer 6 Strict, Mozilla (not FF) and Safari
    return document.body.offsetHeight;
  }
}


//-------------------------------------------------

if (!this.JSON) {
    JSON = {};
}
(function () {

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return this.getUTCFullYear()   + '-' +
                 f(this.getUTCMonth() + 1) + '-' +
                 f(this.getUTCDate())      + 'T' +
                 f(this.getUTCHours())     + ':' +
                 f(this.getUTCMinutes())   + ':' +
                 f(this.getUTCSeconds())   + 'Z';
        };

        String.prototype.toJSON =
        Number.prototype.toJSON =
        Boolean.prototype.toJSON = function (key) {
            return this.valueOf();
        };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ?
            '"' + string.replace(escapable, function (a) {
                var c = meta[a];
                return typeof c === 'string' ? c :
                    '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            }) + '"' :
            '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0 ? '[]' :
                    gap ? '[\n' + gap +
                            partial.join(',\n' + gap) + '\n' +
                                mind + ']' :
                          '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    k = rep[i];
                    if (typeof k === 'string') {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0 ? '{}' :
                gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' +
                        mind + '}' : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                     typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/.
test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function' ?
                    walk({'': j}, '') : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
})();
