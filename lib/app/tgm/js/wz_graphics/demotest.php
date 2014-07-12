<html>
<head>
<script type="text/javascript" src="jsutils.js"></script>
<script type="text/javascript" src="wz_jsgraphics.js"></script>
<script type="text/javascript">
function drawLine()
 {
        // Font Styles:  Font.PLAIN for normal style (not bold, not italic)
        //               Font.BOLD for bold fonts
        //               Font.ITALIC for italics
        //               Font.ITALIC_BOLD or Font.BOLD_ITALIC to combine the latters


   var height=360;
   var width =400;
   var x=30;
   var y=20;
   var g = new jsGraphics("CANVAS");
   g.setColor("#0000AA");
   g.setStroke(2);
   g.setFont("Arial", "16px", Font.BOLD);

   var xPoints  = new Array( x,  x,         x+width,  x);
   var yPoints  = new Array( y,  y+height,  y+height, y);

   g.drawPolyline(xPoints, yPoints);

   g.fillRect(x, y+height-20, 20, 20);

   g.drawString("Hypotenuse", x+parseInt(width/2)+50, y+parseInt(height/2));

   g.paint();


   var el = newGraphicsLayer("test", {w:100, h:28}, {x:500, y:20});
   // var el = newGraphicsLayer("test");

   el.style.backgroundColor='#FF8080';
   el.innerHTML="Hello There";
   // alert("html: "+el.innerHTML);

   dd.elements['test'].resizeTo(200,22);
   dd.elements['test'].moveTo(10,10);

   // or dd.elements.test.moveTo(10,10);

   return;
   //----------------------------------



   g.setColor("#00ff00"); // green
   g.fillEllipse(100, 200, 100, 180); // co-ordinates related to the document
   g.paint();

   g.setColor("#ff0000"); // red
   g.drawLine(10, 113, 220, 55); // co-ordinates related to "myCanvas"
   g.setColor("#0000ff"); // blue
   g.fillRect(110, 120, 30, 60);
   g.paint();

   g.setColor("#0000ff"); // blue
   g.drawEllipse(10, 50, 30, 100);
   g.drawRect(400, 10, 100, 50);
   g.paint();

 }

function newGraphicsLayer(id,size,pos)
 {
   var el = document.createElement("div");
   el.setAttribute("id", id);
   el.style.position = "absolute";
   if (arguments.length>=2)  // size
     {
       el.style.width  = size.w;
       el.style.height = size.h;
     }
   if (arguments.length>=3)  // pos
     {
       el.style.left   = pos.x;
       el.style.top    = pos.y;
     }
   var mybody  = document.getElementsByTagName("body")[0];
   mybody.appendChild(el);
   ADD_DHTML(id);
   return el;
 }
</script>
</head>
<body>
<script type="text/javascript" src="wz_dragdrop.js"></script>

<br>&nbsp;<br>
<br>&nbsp;<br>

    <a href=javascript:drawLine()>Draw Line</a>



<div id="CANVAS" style="position:absolute; top:200; left:200; background-color:#E0E0E0; height:400px; width:500;"></div>


              <div id="content1" style="position:absolute; top:10; left:100">Content 1</div>.
              <div id="content2" style="position:absolute; top:10; left:200">Content 2</div>.
              <div id="content3" style="position:absolute; top:10; left:300">Content 3</div>.


<script type="text/javascript">

  /*---------------------------------------------------------------------------------------
      Names for Drag&Drop elements
      Images: Each of the images to be set draggable requires a unique name,
              for example:  <img name="name1" src="someImg.jpg" width="240" height="135">
              Width and height attributes are mandatory and should be absolute numbers
              like width="240", rather than relative ones like width="33%".
      Layers: Each one requires a unique ID and, contrary to images, must be positioned
              relatively or absolutely:
              <div id="name2" style="position:absolute;...">Content</div>.
  -----------------------------------------------------------------------------------------*/

  SET_DHTML();
  ADD_DHTML("content2");
  ADD_DHTML("content3");

</script>
</body>
</html>
