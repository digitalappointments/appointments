var _MONITOR_        = "monitor";
var _MONITOR_CLOSE_  = "monitor_close";
var _PLAYER_ID_      = "vplayer";


var center_adjust = -15; //visually compensate for shadow
var close_vdelta  = 296;
var close_hdelta  = 540;
var player_vdelta = 13;
var player_hdelta = 50;
var monitor_rollup_timer_interval = 30; // milliseconds: 40=slow 30=medium 20=fast
var _system_config=null;
var _active_video=null;

var _video_={};

function mouse_over(obj)
  {
    obj.style.cursor = "pointer";
  }

function pgwidth()
 {
   return window.innerWidth != null? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body != null ? document.body.clientWidth : null;
 }

function _lights_down() {
  var vel=document.getElementById('RoomDarkeningShade');
  if (vel)
    {
      vel.style.display='block';
      _reposition_();
      _add_Event(window, "resize", _reposition_);
      _add_Event(window, "scroll", _reposition_);
      window.onscroll = _reposition_;

    }
}

function _lights_up() {
  var vel=document.getElementById('RoomDarkeningShade');
  if (vel)
    {
      vel.style.display='none';
      _reposition_();
      _remove_Event(window, "resize", _reposition_, true);
      _remove_Event(window, "scroll", _reposition_, true);
      window.onscroll = null;
    }
}


function keep_center(v)
  {
//    _set_mask_size();
//    v.center(center_adjust);
//    var f = function() { keep_center(v); }
//    setTimeout(f, 50);
  }


function _reposition_()
  {
    if (_active_video != null)
      {
        _set_mask_size();
        _active_video.center(center_adjust);
      }
  }


//---- Simple Public APIs - Open/Close ----
function openVideo(file)
  {
    window.scrollTo(0,0);
    _open_Video(_MONITOR_,_MONITOR_CLOSE_,_PLAYER_ID_,file);
  }

function closeVideo()
  {
    _close_Video(_MONITOR_);
  }
//-----------------------------------------

function _open_Video(monitor_id,close_id,player_id,file)
  {
    var v;

    if (_system_config==null)
      {
         system_setConfig();
         var bg = "http://pds1106.s3.amazonaws.com/images/maskBG.png";
         var theBody = document.getElementsByTagName('BODY')[0];
         var mask = document.createElement('div');
         mask.id  = 'RoomDarkeningShade';
         mask.style.position = "absolute";
         mask.style.top    = 0 + 'px';
         mask.style.left   = 0 + 'px';
         mask.style.width  = 0 + 'px';
         mask.style.height = 0 + 'px';
         mask.style.backgroundImage ="url("+ bg +")";
         mask.style.backgroundRepeat="repeat";
         mask.style.backgroundColor ="transparent";
         mask.style.display='none'; // hide it
         theBody.appendChild(mask);
      }

    if (typeof _video_[monitor_id] == 'undefined')
      {
        v = new Video(monitor_id,close_id,player_id);
        _video_[monitor_id]={
                status:    0,
                close_id:  close_id,
                player_id: player_id,
                video: v
        }
        keep_center(v);
      }
    else
      {
        v = _video_[monitor_id].video;

        if (_video_[monitor_id].status!=0) return;  // added - to Only Allow Play if Monitor is Fully Retracted

        if (_video_[monitor_id].status==1) return;  // Screen is in Motion
        if (_video_[monitor_id].status==2) // Already Open
          {
            v.initialize(file);
            v.play(file);
            return;
          }
      }

    _active_video=v;
    _reposition_();

//  v.center(center_adjust);

    _video_[monitor_id].status=1; // Screen is in Motion

//  _lights_down();  // Turn lights down before extending screen

    v.open( function()
      {
        v.center(center_adjust);
        v.show();

        _video_[monitor_id].status=2;  // open

           _lights_down();   // Turn lights down after screen is fully extended

        v.initialize(file);
        v.play(file);
      }
    );
  }


function _close_Video(monitor_id)
  {
    var v;
    if (_video_[monitor_id].status!=2) return;

    _video_[monitor_id].status=1; // Screen is in Motion

    _lights_up();   // Turn lights up before retracting screen

    _active_video=null;

    if (typeof _video_[monitor_id] == 'object')
      {
        v = _video_[monitor_id].video;

        v.close( function()
          {
            _video_[monitor_id].status=0;  // Screen is Fully Retracted

            // _lights_up();    // Turn lights up after screen is fully retracted

          }
        );
      }
  }



function _set_mask_size() {
   var mask=document.getElementById('RoomDarkeningShade');
   if (mask)
     {
       if (mask.style.display != 'none')
         {
           var pageSize = _get_page_size();
           mask.style.width  = pageSize.w + 'px';
           mask.style.height = pageSize.h + 'px';
         }
     }
}


function _get_page_size() {
    return { w: _system_config.getScrollX()+_system_config.getWndW(), h: _system_config.getScrollY()+_system_config.getWndH() };
}


/***----
function _get_page_size2() {
    var theBody = document.getElementsByTagName("BODY")[0];

    var fullHeight = _get_Viewport_Height();
    var fullWidth  = _get_Viewport_Width();
    var pageWidth  = 0;
    var pageHeight = 0;

    // Determine what's bigger, scrollHeight or fullHeight / width
    if (fullHeight > theBody.scrollHeight) {
        pageHeight = fullHeight;
    } else {
        pageHeight = theBody.scrollHeight;
    }

    if (fullWidth > theBody.scrollWidth) {
        pageWidth = fullWidth;
    } else {
        pageWidth = theBody.scrollWidth;
    }
    return { w: pageWidth, h: pageHeight};
}

function _get_Viewport_Height() {
    if (window.innerHeight!=window.undefined) return window.innerHeight;
    if (document.compatMode=='CSS1Compat') return document.documentElement.clientHeight;
    if (document.body) return document.body.clientHeight;
    return window.undefined;
}

function _get_Viewport_Width() {
    if (window.innerWidth!=window.undefined) return window.innerWidth;
    if (document.compatMode=='CSS1Compat') return document.documentElement.clientWidth;
    if (document.body) return document.body.clientWidth;
}
----***/


function _System_Config_()
  {
    this.elements = new Array(0);
    this.obj = null;
    this.n = navigator.userAgent.toLowerCase();
    this.db = (document.compatMode && document.compatMode.toLowerCase() != "backcompat")?
        document.documentElement
        : (document.body || null);
    this.op = !!(window.opera && document.getElementById);
    if(this.op) document.onmousedown = new Function('e',
        'if(((e = e || window.event).target || e.srcElement).tagName == "IMAGE") return false;');
    this.ie = !!(this.n.indexOf("msie") >= 0 && document.all && this.db && !this.op);
    this.iemac = !!(this.ie && this.n.indexOf("mac") >= 0);
    this.ie4 = !!(this.ie && !document.getElementById);
    this.n4 = !!(document.layers && typeof document.classes != "undefined");
    this.n6 = !!(typeof window.getComputedStyle != "undefined" && typeof document.createRange != "undefined");
    this.w3c = !!(!this.op && !this.ie && !this.n6 && document.getElementById);
    this.ce = !!(document.captureEvents && document.releaseEvents && !this.n6);
    this.px = this.n4? '' : 'px';
    this.tWait = this.w3c? 40 : 10;
    this.noRecalc = false;
  }


function system_setConfig()
  {
    _system_config = new _System_Config_();
    _system_config.Int = function(d_x, d_y)
      {
        return isNaN(d_y = parseInt(d_x))? 0 : d_y;
      };
    _system_config.getWndW = function()
      {
        return _system_config.Int(
           (_system_config.db && !_system_config.op && !_system_config.w3c && _system_config.db.clientWidth)? _system_config.db.clientWidth
           : (window.innerWidth || 0)
        );
      };
    _system_config.getWndH = function()
      {
        return _system_config.Int(
          (_system_config.db && !_system_config.op && !_system_config.w3c && _system_config.db.clientHeight)? _system_config.db.clientHeight
          : (window.innerHeight || 0)
        );
      };
    _system_config.getScrollX = function()
      {
        return _system_config.Int(window.pageXOffset || (_system_config.db? _system_config.db.scrollLeft : 0));
      };
    _system_config.getScrollY = function()
      {
         return _system_config.Int(window.pageYOffset || (_system_config.db? _system_config.db.scrollTop : 0));
      };
  }



/**
 * COMMON DHTML FUNCTIONS
 * These are handy functions I use all the time.
 *
 * By Seth Banks (webmaster at subimage dot com)
 * http://www.subimage.com/
 *
 * Up to date code can be found at http://www.subimage.com/dhtml/
 *
 * This code is free for you to use anywhere, just keep this comment block.
 */

/**
 * X-browser event handler attachment and detachment
 * TH: Switched first true to false per http://www.onlinetools.org/articles/unobtrusivejavascript/chapter4.html
 *
 * @argument obj - the object to attach event to
 * @argument evType - name of the event - DONT ADD "on", pass only "mouseover", etc
 * @argument fn - function to call
 */
function _add_Event(obj, evType, fn){
 if (obj.addEventListener){
    obj.addEventListener(evType, fn, false);
    return true;
 } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
 } else {
    return false;
 }
}
function _remove_Event(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}


//---------------------------------------------------
// Video Class
//---------------------------------------------------

function Video(monitor_id,close_id,player_id) {
  this.monitor_id = monitor_id;
  this.close_id   = close_id;
  this.player_id  = player_id;

  this.speed      = 10;            // larger is Faster

  this.monitor      = document.getElementById(this.monitor_id);
  this.player       = document.getElementById(this.player_id);
  this.close_button = document.getElementById(this.close_id);

  this.monitor.style.display="";
  this.width        = parseInt(this.monitor.getAttribute("width"));
  this.height       = parseInt(this.monitor.getAttribute("height"));
  this.monitor.style.display="none";

  this.player.style.display="none";
  this.close_button.style.display="none";
  this.ratio        = this.width/this.height;

  this.initialize = function()
    {
      var video_player =
        {
          // our Flash component
          src: "http://pds1106.s3.amazonaws.com/player/flowplayer-3.1.2.swf",

          // we need at least this Flash version
          version: [9,115],

          wmode: 'transparent',

          // older versions will see a custom message
          onFail: function()
            {
              var msg  = "You need to upgrade your Flash version for this site.\n";
                  msg += "Your version is " + this.getVersion() + "\n";
              alert(msg);
            }
        }


      var video_settings =
        {
           onLoad: function()
             {    // called when player has finished loading
                this.setVolume(80);    // set volume property
             },

           plugins:
             {
               controls:
                 {
                    autoHide:'always',
                    backgroundGradient: 'high',
                    volumeSliderGradient: 'none',
                    buttonColor: '#000000',
                    timeColor: '#69CDFA',
                    bufferGradient: 'none',
                    progressGradient: 'none',
                    sliderColor: '#C9C9C9',
                    progressColor: '#FFA000',
                    bufferColor: '#C78000',
                    backgroundColor: '#929292',
                    durationColor: '#ffffff',
                    sliderGradient: 'none',
                    volumeSliderColor: '#FFA000',
                    buttonOverColor: '#FFA000',
                    timeBgColor: '#262626',
                    height: 20,
                    hideDelay: 2500,
                    borderRadius: '15',
                    width: '90%',
                    bottom: 3,
                    left: '50%',
                    opacity: 0.7,
                    // tooltips configuration
                    tooltips: {
                        // enable english tooltips on all buttons
                        buttons: true,
                        // customized texts for buttons
                        play: 'Play',
                        pause: 'Pause',
                        fullscreen: 'Full Screen'
                    },

                       // background color for all tooltips
                    tooltipColor: '#C9C9C9',
                       // text color
                    tooltipTextColor: '#000000'
                 }
             }
        }

      this.video_player   = video_player;
      this.video_settings = video_settings;
      flowplayer(this.player_id, this.video_player, this.video_settings);
    }


  this.play = function(file)
    {
       var clip =
         {
           url: file,
           autoPlay: true,
           fadeInSpeed: 1000,
           fadeOutSpeed: 1000,
           autoBuffering: true,
           metaData: false,
             // Setting which defines how video is scaled on the video screen. Available options are:
             //  * fit:   Fit to window by preserving the aspect ratio encoded in the file's metadata.
             //  * half:  Half-size (preserves aspect ratio)
             //  * orig:  Use the dimensions encoded in the file. If too big - then 'scale'
             //  * scale: (DEFAULT) Scale the video to fill all available space. Ignores dimensions in metadata
           scaling: 'scale'
         }
       flowplayer(player_id).play(clip);
    }

  this.center = function(delta)
    {
      var pw2   =Math.round(parseInt(pgwidth()/2));
      var width2=Math.round(parseInt(this.width/2));
      var lft   =pw2-width2;
      if (arguments.length >= 1)
        lft += delta;
      this.monitor.style.left = lft+"px";
      this.close_button.style.left = (lft+close_hdelta)  + "px";
      this.player.style.left       = (lft+player_hdelta) + "px";
    }

  this.hide = function()
    {
      this.monitor.style.display="none";
      this.player.style.display="none";
      this.close_button.style.display="none";
    }
  this.show = function()
    {
      this.monitor.style.display="";
      this.player.style.display="";
      this.close_button.style.display="";
    }

  this.open = function(cb)
    {
      this.monitor.style.top = -this.height + 'px';
      this.hide();
      this.monitor.style.display="";
      this.roll_down(this,-this.height,cb);
    }

  this.close = function(cb)
    {
      this.monitor.style.top = '0px';
      flowplayer(this.player_id).unload();
      this.hide();
      this.monitor.style.display="";
      this.roll_up(this,0,cb);
    }

  this.dump = function()
    {
      var msg="";
      msg += "width:  "+this.width+"\n";
      msg += "height: "+this.height+"\n";
      alert(msg);
    }

  function private_roll_down(obj,t,cb)
    {
      obj.monitor.style.top = t + 'px';

      // log("t="+t);

      if (t<0)
        {
          t += obj.speed;
          if (t > 0) t=0;
          var f = function() { private_roll_down(obj,t,cb); }
          setTimeout(f,monitor_rollup_timer_interval);
        }
      else
        {
          if (cb)
            cb();
        }
    }
  this.roll_down = private_roll_down;


  function private_roll_up(obj,t,cb)
    {
      obj.monitor.style.top = t + 'px';

      // log("t="+t);

      if (t+obj.height > 0)
        {
          t -= obj.speed;
          if (t+obj.height < 0) t=-obj.height;
          var f = function() { private_roll_up(obj,t,cb); }
          setTimeout(f,monitor_rollup_timer_interval);
        }
      else
        {
          obj.monitor.style.display="none";
          if (cb)
            cb();
        }
    }
  this.roll_up = private_roll_up;

  function log(s)
    {
      var p = document.getElementById('SCRATCH_PAD');
      if (p) p.innerHTML += (s+'\n');
    }
  this.log = log;

  this.initialize();

}
