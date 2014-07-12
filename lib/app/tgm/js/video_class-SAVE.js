var _MONITOR_        = "monitor";
var _MONITOR_CLOSE_  = "monitor_close";
var _PLAYER_ID_      = "vplayer";


var center_adjust = -15; //visually compensate for shadow
var close_vdelta  = 296;
var close_hdelta  = 540;
var player_vdelta = 13;
var player_hdelta = 50;
var monitor_rollup_timer_interval = 30; // milliseconds: 40=slow 30=medium 20=fast

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
  var el = document.getElementById("PUSHY_MEMBER_SITE");
  if (el)
    el.className="LightsDown";
}

function _lights_up() {
  var el = document.getElementById("PUSHY_MEMBER_SITE");
  if (el)
    el.className="";
}

function keep_center(v)
  {
    v.center(center_adjust);
    var f = function() { keep_center(v); }
    setTimeout(f, 100);
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


    v.center(center_adjust);
    _video_[monitor_id].status=1; // Screen is in Motion
    v.open( function()
      {
        v.center(center_adjust);
        v.show();

        _video_[monitor_id].status=2;  // open

        _lights_down();

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
    if (typeof _video_[monitor_id] == 'object')
      {
        v = _video_[monitor_id].video;

        _lights_up();

        v.close( function()
          {
            _video_[monitor_id].status=0;  // Screen is Fully Retracted
          }
        );
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
