jsLoader=null;
function _JS_LOADER_()
 {
   if (jsLoader!=null) return jsLoader;

   var _jsLoaded = {};

   this.isLoaded = function(name) {
      if (_jsLoaded[name])
        return true;
      return false;
   }

   this.load = function(name,url) {
      // this.unload(name);
      if (!this.isLoaded(name))
        {
           var scriptHandle=document.createElement('script')
           scriptHandle.setAttribute("type","text/javascript");
           scriptHandle.setAttribute("src", url);
           document.getElementsByTagName("head").item(0).appendChild(scriptHandle);
           _jsLoaded[name] = {url: url, handle:scriptHandle};
        }
   }

   this.unload = function(name) {
      if (_jsLoaded[name]) {
        var scriptHandle=_jsLoaded[name]['handle'];
        document.getElementsByTagName("head").item(0).removeChild(scriptHandle);
        delete _jsLoaded[name];
      }
   }


   this.show = function() {
      var msg = "";
      var j=0;
      for (var name in _jsLoaded) {
        j++;
        var url = _jsLoaded[name]['url'];
        msg += "  ("+j+")  name: "+name+"   url: "+url+"\n";
      }
      if (msg.length == 0)
        msg="No Js Files Loaded";
      msg+=" \n";
      alert(msg);
   }
 }
jsLoader = new _JS_LOADER_();
