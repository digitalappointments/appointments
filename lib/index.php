<?php
 $args=(!empty($_SERVER['argc']) && $_SERVER['argc'] > 0);
?>

<html>
<title>Appointments</title>
<style>
.text
  {
    font-family: Arial;
    font-size:   16px;
    font-weight: bold;
    <?php
       if ($args) {
    ?>
        color: #0000CC;
    <?php
       } else {
    ?>
        color: #CC0000;
    <?php
       } 
    ?>
  }
</style>
</head>

<table border="0" cellpadding="0" cellspacing="0" width="800">
  <tr>
    <td width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td width="100%" align="center" class="text">Handle My Appointments</td>
  </tr>
</table>

</body>
</html>
