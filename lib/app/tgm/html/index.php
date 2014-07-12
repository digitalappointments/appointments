<html>
<head>
    <?php
    include_once("lib/app/tgm/html/initialize.php");
    $FILE_INFO = getFileInfo(__FILE__);
    if ($FILE_INFO["PathInfo"]["filename"] == 'index') {
        define("CONTENT_PAGE", 'home');
    } else {
        define("CONTENT_PAGE", $FILE_INFO["PathInfo"]["filename"]);
    }
    include_once("lib/app/tgm/html/head.php");
    ?>
</head>

<body bgcolor="#F0E8C0">

<table align="center" width="950" cellpadding=0 cellspacing=0 border=0>
    <?php
    // echo "--- " . CONTENT_PAGE . " ---<br>";
    include_once("lib/app/tgm/html/menu.php");
    ?>
</table>

<!------------------------>
<table align="center" width="950" cellspacing=0 border=1
       style="padding:4px 4px; 4px; 4px; border:1; border-collapse:collapse;">
    <tr>
        <td>
            <div id="page_content">
                <?php
                include_once("lib/app/tgm/content/" . CONTENT_PAGE . ".php");
                ?>
            </div>
        </td>
    </tr>
</table>
<!------------------------>

<table align="center" width="950" cellpadding=0 cellspacing=0 border=0>
    <?php include_once("lib/app/tgm/html/footer.php");?>
</table>

</body>
</html>
