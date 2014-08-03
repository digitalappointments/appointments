<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<style>
.background {
   background-repeat: repeat-x;
   background-image: url('http://pds1106.s3.amazonaws.com/images/gray-stripes.gif');
   background-attachment: fixed;
   background-color: #333333;
   font-family: Tahoma,Verdana,Arial;
   scrollbar-base-color: #FD9B00;
   scrollbar-arrow-color: #B26B00;
   scrollbar-DarkShadow-Color: 753F00;
   color: #333330;
}
.playlist {
   background-repeat: no-repeat;
   background-image: url('http://pds1106.s3.amazonaws.com/player/images/playlist.png');
   background-color: transparent;
}
.playlist_shadow {
   background-repeat: no-repeat;
   background-image: url('http://pds1106.s3.amazonaws.com/player/images/playlist_shadow.png');
   background-color: transparent;
}
.playlist_monitor {
   background-repeat: no-repeat;
   background-image: url('http://pds1106.s3.amazonaws.com/player/images/playlist_monitor.png');
   background-color: transparent;
}
.vdisplay {
   padding-top:5px;
   padding-bottom:10px;
   padding-left:15px;
   font-family: mono;
   font-size: 14px;
}
</style>


<script type="text/javascript" src="http://pds1106.s3.amazonaws.com/player/swfobject.js"></script>
<script type="text/javascript" src="http://pds1106.s3.amazonaws.com/player/vplayer-debug.js"></script>

<title>Flash Player</title>
</head>

<body class=background onLoad="createPlayer()">

  <br> <br>
  <br> <br>

  <form>
  <table width=960 cellpadding=0 cellspacing=0 border=0>
     <tr>
        <td width=160>
            &nbsp;
        </td>
        <td width=649>
          <table cellpadding=0 cellspacing=0 border=0>
            <tr>
              <td width=232 class=playlist valign=top style="padding-top:66px;">
                <table width=232 height=160 cellpadding=0 cellspacing=0 border=0 style="padding-left:46px;">
                  <tr><td width=183 height=53><a href=javascript:loadVideo('http://autoprospector.s3.amazonaws.com/signup/signup1.flv')
                        onMouseOver="document.why_pushy.src='http://pds1106.s3.amazonaws.com/player/images/why_pushy_over.png';"
                        onMouseOut ="document.why_pushy.src='http://pds1106.s3.amazonaws.com/player/images/why_pushy.png';"><img name="why_pushy" src="http://pds1106.s3.amazonaws.com/player/images/why_pushy.png" border=0></a></td></tr>

                  <tr><td width=183 height=53><a href=javascript:loadVideo('http://autoprospector.s3.amazonaws.com/video/video-pm1203211688610833.flv')
                        onMouseOver="document.how_pushy.src='http://pds1106.s3.amazonaws.com/player/images/how_pushy_over.png';"
                        onMouseOut ="document.how_pushy.src='http://pds1106.s3.amazonaws.com/player/images/how_pushy.png';"><img name="how_pushy" src="http://pds1106.s3.amazonaws.com/player/images/how_pushy.png" border=0></a></td></tr>

                  <tr><td width=183 height=53><a href=javascript:loadVideo('http://autoprospector.s3.amazonaws.com/video/video-cw1197666564481748.flv')
                        onMouseOver="document.push_pushy.src='http://pds1106.s3.amazonaws.com/player/images/push_pushy_over.png';"
                        onMouseOut ="document.push_pushy.src='http://pds1106.s3.amazonaws.com/player/images/push_pushy.png';"><img name="push_pushy" src="http://pds1106.s3.amazonaws.com/player/images/push_pushy.png" border=0></a></td></tr>
                </table>
              </td>
              <td width=21  class=playlist_shadow></td>
              <td width=396 class=playlist_monitor>
                <div style="position:relative; z-index:0; width:380px; height:270px; padding-top:10px; padding-left:8px; padding-right:10px;">
                   <div id="vplayer"></div>
                </div>
              </td>
            </tr>
          </table>
        </td>
        <td width=161>
            &nbsp;
        </td>
     </tr>
  </table>
  </form>


  <br> <br>

  <form>
  <table width=960 cellpadding=0 cellspacing=0 border=0>
     <tr height=36 valign=middle>
        <td width=160>
            &nbsp;
        </td>
        <td width=649>
          <table bgcolor="#D0D6DF" width=649 height=30 cellpadding=0 cellspacing=0 border=0>
            <tr>
              <td width="33%" align=center><input type=button value="Start" onClick=javascript:vPlay('vplayer')></td>
              <td width="33%" align=center><input type=button value="Stop"  onClick=javascript:vStop('vplayer')></td>
              <td width="33%" align=center><input type=button value="Pause" onClick=javascript:vPause('vplayer')></td>
            </tr>
          </table>
        </td>
        <td width=161>
            &nbsp;
        </td>
     </tr>

     <tr height=20><td colspan=3>&nbsp;</td></tr>
     <tr>
        <td width=160>
            &nbsp;
        </td>
        <td width=649 bgcolor="#F0F0F0">
             <div class=vdisplay style="font-weight:bold; color:#990000; font-size:18px; " id="VSTATUS"> <br> </div>
        </td>
        <td width=161>
            &nbsp;
        </td>
     </tr>
     <tr height=20><td colspan=3>&nbsp;</td></tr>


     <tr>
        <td width=160>
            &nbsp;
        </td>
        <td width=649 bgcolor="#F0F0F0">
             <div class=vdisplay id="VTIMER">--------------------- TIMER ------------------------<br></div>
             <div class=vdisplay id="MODEL">--------------------- MODEL ------------------------<br></div>
             <div class=vdisplay id="CONTROLLER">--------------- CONTROLLER ------------------------<br></div>
        </td>
        <td width=161>
            &nbsp;
        </td>
     </tr>
  </table>
  </form>

  <br>
  <br>
  <br>


</body>
</html>
