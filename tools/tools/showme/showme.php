<?php

  printf("Method: $REQUEST_METHOD<br><br><br>\n");

  if (is_array($_REQUEST) && count($_REQUEST) > 0)
    {
      printf("-------- REQUEST VARS -- (Get/Post/Cookie/Files) ---<br>\n");
      while (list($key00, $value00) = each($_REQUEST))
        {
          // --- ASSIGN THE VARIABLES
          // $str = "\$".$key00." = $value00;";
          // printf("%s<br>\n",$str);
          // eval($str);

          printf("%s=%s<br>\n",$key00,$value00);
        }
      printf("<br><br><br>\n");
    }



  if (is_array($_FILES) && count($_FILES) > 0)
    {
      $j=0;
      printf("-------- FILES VARS ---------<br>\n");
      while (list($k, $v) = each($_FILES))
        {
          if (is_array($v))
            {
               $j++;
               printf("<br>\n   ..... FILE(%d):  '%s'<br>\n",$j,$k);
               while (list($k2, $v2) = each($v))
                 {
                    printf("%s=%s<br>\n",$k2,$v2);
                 }
               printf("<br>\n");

            }
        }
      printf("<br><br><br>\n");
    }


//  if (is_array($_GET) && count($_GET) > 0)
//    {
//      printf("-------- GET VARS --------<br>\n");
//      while (list($key00, $value00) = each($_GET))
//        {
//          // --- ASSIGN THE VARIABLES
//          // $str = "\$".$key00." = $value00;";
//          // printf("%s<br>\n",$str);
//          // eval($str);
//
//          printf("%s=%s<br>\n",$key00,$value00);
//        }
//      printf("<br><br><br>\n");
//    }


//  if (is_array($_POST) && count($_POST) > 0)
//    {
//      printf("-------- POST VARS --------<br>\n");
//      while (list($key00, $value00) = each($_POST))
//        {
//          // --- ASSIGN THE VARIABLES
//          // $str = "\$".$key00." = $value00;";
//          // printf("%s<br>\n",$str);
//          // eval($str);
//
//          printf("%s=%s<br>\n",$key00,$value00);
//        }
//    }



  if (is_array($_SERVER) && count($_SERVER) > 0)
    {
      printf("-------- SERVER VARS ---------<br>\n");
      while (list($key00, $value00) = each($_SERVER))
        {
          printf("%s=%s<br>\n",$key00,$value00);
        }
      printf("<br><br><br>\n");
    }


//  $_FILES['userfile']['name']     - The original name of the file on the client machine.
//  $_FILES['userfile']['type']     - The mime type of the file, if the browser provided this information.
//                                    An example would be "image/gif". This mime type is however not checked
//                                    on the PHP side and therefore don't take its value for granted.
//  $_FILES['userfile']['size']     - The size, in bytes, of the uploaded file.
//  $_FILES['userfile']['tmp_name'] - The temporary filename of the file in which the uploaded file was stored on the server.
//  $_FILES['userfile']['error']    - The error code associated with this file upload. This element was added in PHP 4.2.0









//  if (is_array($_ENV) && count($_ENV) > 0)
//    {
//      printf("-------- ENV VARS ---------<br>\n");
//      while (list($key00, $value00) = each($_ENV))
//        {
//          printf("%s=%s<br>\n",$key00,$value00);
//        }
//      printf("<br><br><br>\n");
//    }


  exit;
?>
