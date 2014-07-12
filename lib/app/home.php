<html>
<head>
    <?php
    include_once("lib/app/initialize.php");
    $FILE_INFO = getFileInfo(__FILE__);
    if ($FILE_INFO["PathInfo"]["filename"] == 'index') {
        define("PAGE_NAME", 'home');
    } else {
        define("PAGE_NAME", $FILE_INFO["PathInfo"]["filename"]);
    }
    include_once("lib/app/head.php");
    ?>
</head>

<body bgcolor="#F0E8C0">

<table align="center" width="950" cellpadding=0 cellspacing=0 border=0>
    <?php
    // echo "--- " . PAGE_NAME . " ---<br>";
    include_once("lib/app/menu.php");
    ?>
</table>

<!------------------------>
<table align="center" width="950" cellspacing=0 border=1
       style="padding:4px 4px; 4px; 4px; border:1; border-collapse:collapse;">
    <tr>
        <td>
            <div id="page_content">
                <?php
                include_once("lib/app/content/" . PAGE_NAME . ".php");
                ?>
            </div>
        </td>
    </tr>
</table>
<!------------------------>

<table align="center" width="950" cellpadding=0 cellspacing=0 border=0>
    <?php include_once("lib/app/footer.php");?>
</table>

</body>
</html>

