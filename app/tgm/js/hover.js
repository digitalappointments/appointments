<script>
if (!document.layers)
document.write('<div id="divStayTopLeft" style="position:absolute">')
</script>

<layer id="divStayTopLeft">
   <img src="http://pds1106.s3.amazonaws.com/images/pushy_float.png">
</layer>

<script type="text/javascript">

/*
Floating Menu script-  Roy Whittle (http://www.javascript-fx.com/)
Script featured on/available at http://www.dynamicdrive.com/
This notice must stay intact for use
*/

if (!document.layers)
document.write('</div>')

function JSFX_FloatTopDiv(verticalOrigin,verticalOffset)
{
    var height=0;
    var startX = 3,
    startY = 0;
    var ns = (navigator.appName.indexOf("Netscape") != -1);
    var d = document;
    function ml(id)
    {
        var el=d.getElementById?d.getElementById(id):d.all?d.all[id]:d.layers[id];
        height=el.offsetHeight;
        if(d.layers)el.style=el;
        el.sP=function(x,y){this.style.left=x;this.style.top=y;};
        el.x = startX;
        if (verticalOrigin=="fromtop")
          el.y = startY;
        else{
          el.y = ns ? pageYOffset + innerHeight : document.body.scrollTop + document.body.clientHeight;
          el.y -= height;
        }
        return el;
    }
    window.stayTopLeft=function()
    {
        if (verticalOrigin=="fromtop"){
          var pY = ns ? pageYOffset : document.body.scrollTop;
          ftlObj.y += (pY + startY + verticalOffset - ftlObj.y)/8;
        }
        else{
          var pY = ns ? pageYOffset + innerHeight : document.body.scrollTop + document.body.clientHeight;
          ftlObj.y += (pY - (startY+height+verticalOffset) - ftlObj.y)/8;
        }
        ftlObj.sP(ftlObj.x, ftlObj.y);
        setTimeout("stayTopLeft()", 10);
    }
    ftlObj = ml("divStayTopLeft");
    stayTopLeft();
}
JSFX_FloatTopDiv("fromtop",0);
// JSFX_FloatTopDiv("frombottom",5);
</script>
