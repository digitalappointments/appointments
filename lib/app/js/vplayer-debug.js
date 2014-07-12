var player={};
var _player_debug_=true;

function createPlayer(id,width,height,file) {
    var flashvars = {
         file:file,
         autostart:"true",
         controlbar:"over",
         //image:"http://pds1106.s3.amazonaws.com/images/32.jpg",
         stretching:"fill"
    }
    var params = {
         allowfullscreen:"true",
         allowscriptaccess:"always",
         wmode: "transparent"
    }
    var attributes = {
         id:    id,
         name:  id,
         wmode: "transparent"
    }

    swfobject.embedSWF("http://pds1106.s3.amazonaws.com/player/player.swf", id, width, height, "9.0.115", false, flashvars, params, attributes);
}


function playerReady(obj) {
    // var msg  = "The videoplayer has been instantiated \n";
    //     msg += "  id: "+obj['id']+"\n";
    //     msg += "  version: "+obj['version']+"\n";
    //     msg += "  client:  "+obj['client']+"\n";
    // alert(msg);

    // alert('the videoplayer '+obj['id']+' has been instantiated');

    var id=obj['id'];

    player[id] = {};
    var vp = document.getElementById(id);
    player[id].element=vp;

    var config = vp.getConfig();
    var fileLoaded=config['file'];
    player[id].state=
      {
        oldState: '',
        currentState: '',
        fileLoaded: fileLoaded,
        pauseOnStartup: true,
        isPaused: false
      }

    if (_player_debug_)
      {
        showConfig(id);
      }
    addListeners(id);
}


function volumeListener(obj)
  {
    var id = obj['id'];
    var vp = player[id].element;
    // var currentVolume = obj.percentage;
    // alert("Volume="+currentVolume);
  }

function addListeners(id) {

    var vp = player[id].element;
    if (vp) {
        vp.addModelListener     ("META",   "metaStateListener");
        vp.addModelListener     ("BUFFER", "modelBufferListener");
        vp.addModelListener     ("LOADED", "modelLoadedListener");
        vp.addModelListener     ("STATE",  "modelStateListener");
        vp.addModelListener     ("TIME",   "modelTimeListener");
        vp.addModelListener     ("ERROR",  "modelErrorListener");

        vp.addControllerListener("VOLUME", "controllerVolumeListener");
        vp.addControllerListener("PLAY",   "controllerPlayListener");
        vp.addControllerListener("STOP",   "controllerStopListener");
        vp.addControllerListener("MUTE",   "controllerMuteListener");

        vp.addControllerListener("SEEK",   "controllerSeekListener");
        vp.addControllerListener("ITEM",   "controllerItemListener");
        vp.addControllerListener("RESIZE", "controllerResizeListener");
        vp.addControllerListener("ERROR",  "controllerErrorListener");
    } else {
        var f = function() { addListeners(id,100); }
        setTimeout(f,100);
    }
}


function showConfig(id) {
  var vp = player[id].element;
  var config = vp.getConfig();
  var msg="";

  for (var k in config) {
    if (typeof config[k] != 'undefined')
       msg += k+" => "+config[k]+"\n";
  }
  msg += "\n-- Undefined --\n";
  for (var k in config) {
    if (typeof config[k] == 'undefined')
       msg += k+"\n";
  }
  alert(msg);
}


function loadVideo(id,file) {
  var vp = player[id].element;
  showStatus('Loading ... '+file);
  vp.sendEvent('LOAD',file);
  clearStatus();
  player[id].state=
    {
      oldState: '',
      currentState: '',
      fileLoaded: file,
      pauseOnStartup: true
    }
}

function vPlay(id) {
  var vp    = player[id].element;
  // alert("CurrentState="+player[id].state.currentState);
  if (player[id].state.currentState != 'PLAYING')
    vp.sendEvent('PLAY'); // Play
}

function vStop(id) {
  var vp = player[id].element;
  vp.sendEvent('STOP');
}

function vPause(id) {
  var vp = player[id].element;
  if (player[id].state.isPaused) return;
  if (player[id].state.currentState == 'PLAYING')
    {
      vp.sendEvent('PLAY'); // Pause
    }
}







/**---------------------------------- CONTROLLER Event Listeners --------------------------*/
function controllerPlayListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   // alert ("Play: State="+obj.state);
   postControllerEvent(id, 'CTL_PLAY',   ""+obj.state)
 }

function controllerStopListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   // alert ("Stopped");
   postControllerEvent(id, 'CTL_STOP',   "")
 }

function controllerVolumeListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postControllerEvent(id, 'CTL_VOLUME', ""+obj.state);
 }

function controllerMuteListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postControllerEvent(id, 'CTL_MUTE',   ""+obj.state);
 }

function controllerSeekListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postControllerEvent(id, 'CTL_SEEK',   ""+obj.state);
 }

function controllerItemListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postControllerEvent(id, 'CTL_ITEM',   ""+obj.index);
 }

function controllerResizeListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postControllerEvent(id, 'CTL_RESIZE', ""+obj.state);
 }

function controllerErrorListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postControllerEvent(id, 'CTL_ERROR',  ""+obj.state);
 }




/**---------------------------------- MODEL Event Listeners --------------------------*/
function modelBufferListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postModelEvent(id, 'MDL_BUFFER',   ""+obj.percentage+" %")
 }

function modelLoadedListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postModelEvent(id, 'MDL_LOADED',   ""+obj.loaded+" : "+obj.total+" : "+obj.offset)
 }

function modelMetaListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postModelEvent(id, 'MDL_META',     ""+obj.state);
 }

function modelTimeListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   if (player[id].state.pauseOnStartup)
     {
       player[id].state.pauseOnStartup=false;
       vPause(id);
     }
   // postModelEvent(id, 'MDL_TIME',     "Pos:"+obj.position+" Dur:"+obj.duration);
   postTimeEvent(id,obj.position, obj.duration);
 }

function modelErrorListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   postModelEvent(id, 'MDL_ERROR',    ""+obj.state);
 }

function modelStateListener(obj)
 {
   var id = obj['id'];
   var vp = player[id].element;
   player[id].state.oldState = obj.oldstate;
   player[id].state.currentState = obj.newstate;

   postStateEvent(id,obj.oldstate,obj.newstate);

   showStatus("State Listener ..."+obj.oldstate+"   "+obj.newstate);

   if (obj.newstate=="BUFFERING" && obj.oldstate != "BUFFERING")
       showStatus("Video Started");
   else
   if (obj.newstate=="PAUSED" && obj.oldstate != "PAUSED")
     {
       player[id].state.isPaused=true;
       showStatus("Video Paused");
     }
   else
   if (obj.newstate=="PLAYING" && obj.oldstate != "PLAYING")
      showStatus("Video Playing");
   else
   if (obj.newstate=="IDLE" && obj.oldstate != "IDLE")
      showStatus("Video Idle (Stopped)");
   else
   if (obj.newstate=="COMPLETED" && obj.oldstate != "COMPLETED")
     {
       showStatus("Video Complete");
       loadVideo(id,player[id].state.fileLoaded);
     }
 }


function postControllerEvent(id, event, data)
  {
    if (_player_debug_)
      {
        document.getElementById('CONTROLLER').innerHTML += "(" + id + "): " + event + ":  (" + data +")<br>";
      }
  }

function postTimeEvent(id, position, duration)
  {
    if (_player_debug_)
      {
        document.getElementById('VTIMER').innerHTML = "<big><b>POS: </b>"+position+"<br><b>DUR: </b>"+duration+"</big>";
        if (position > 3 && position < 4)
          {
            position = parseInt(duration) - 4;
            // alert(position);
            vplayer.sendEvent("SEEK",position);
          }
      }
  }

function postStateEvent(id, oldState, newState)
  {
    if (_player_debug_)
      {
        document.getElementById('MODEL').innerHTML  += "(" + id + "): MDL_STATE :  ( old: " + oldState + " ==> new: " + newState + " )<br>";
      }
  }

function postModelEvent(id, event, data)
  {
    if (_player_debug_)
      {
        document.getElementById('MODEL').innerHTML += "(" + id + "): " + event + ":  (" + data +")<br>";
      }
  }

//----------------------------

function showStatus(s)
 {
   if (_player_debug_)
     {
       var el = document.getElementById('VSTATUS');
       if (el)
         el.innerHTML=s;
     }
 }

function clearStatus(s)
 {
   if (_player_debug_)
     {
       showStatus(' ');
     }
 }
