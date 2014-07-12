ContentInfo = "";
topColor = "#485975"
subColor = "#FFFFA8"
var tip_active = 0;
var last_tip_id="";
var ToolTipTimeOut;
var ToolTipStyle;

function EnterContent(TTitle, TContent)
 {
    ContentInfo = '<table style="border: 1px solid #CC0000;" width="120" cellspacing="0" cellpadding="0">'+
      '<tr><td width="100%" bgcolor="#000000">'+
      '<table border="0" width="100%" cellspacing="0" cellpadding="0">'+
      '<tr><td width="100%" bgcolor='+topColor+' background="http://pds1106.s3.amazonaws.com/images/bg-tooltip-tp.jpg">'+
      '<table border="0" width="100%" cellspacing="0" cellpadding="2" align="center">'+
      '<tr><td width="100%">'+

      '<font class="tooltiptitle">&nbsp;'+TTitle+'</font>'+

      '</td></tr>'+
      '</table>'+
      '</td></tr>'+
      '<tr><td width="100%" bgcolor='+subColor+' background="http://pds1106.s3.amazonaws.com/images/bg-tooltip.jpg">'+
      '<table border="0" width="100%" cellpadding="0" cellspacing="10" align="center">'+
      '<tr><td width="100%">'+

      '<font class="tooltipcontent">'+TContent+'</font>'+

      '</td></tr>'+
      '</table>'+
      '</td></tr>'+
      '</table>'+
      '</td></tr>'+
      '</table>';
 }


function ad_over(obj,title,content,id)
  {
    if (tip_active==1 && id == last_tip_id) return;
    tip_active=1;
    last_tip_id=id;
    var el = document.getElementById(id);
    if (el)
      {
         var tooltip = document.getElementById('ToolTip');
         if (tooltip)
           {
             var x=findPosX(el);
             var y=findPosY(el);
             var w=el.offsetWidth;
             var h=el.offsetHeight;

             tooltip.style.left = x + 'px';
             tooltip.style.top  = y + 'px';

             var ie = document.all?true:false;
             if ((!ie) && (ToolTipTimeOut))
               {
                 clearTimeout(ToolTipTimeOut);
               }

             tip_active = 1;
             tooltip.style.visibility="visible";

             var d_title=decodeURIComponent(title).replace(/\+/g, ' ');
             var d_content=decodeURIComponent(content).replace(/\+/g, ' ');

             EnterContent(d_title, d_content);
             tooltip.innerHTML = ContentInfo;
           }
         else
           {
             alert("tooltip not found");
           }
      }
  }


function ad_out(obj, id)
  {
    tip_active=0;
    var ie = document.all?true:false;
    var tooltip = document.getElementById('ToolTip');
    if (tooltip)
      {
        if (ie)
          {
            tooltip.style.visibility='hidden';
          }
        else
          {
            ToolTipStyle=tooltip.style;
            ToolTipTimeOut = setTimeout("ToolTipStyle.visibility='hidden';",400);
          }
      }
  }
