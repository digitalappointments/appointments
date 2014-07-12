<?php
 if (file_exists("c:\\windows") || file_exists("c:\\winnt"))
   {
     define("IS_LOCAL",  TRUE);
     define("SECURE_SERVER",      "http://tgm_secure");
     define("TGM_DOCROOT",        "/xampp/tgm/www");
     define("LOCALHOST",          "tgm.local");
   }
 else
   {
     define("IS_LOCAL",  FALSE);
     define("SECURE_SERVER",      "https://wolftracks.securesites.net");
     define("TGM_DOCROOT",        "/home/tgm/www");
     define("LOCALHOST",          "tgm.wolftracks.org");
   }


 define("DOMAIN",  "http://".LOCALHOST);
 define("SITE_NAME",  "SentryTGM.com");

 define("LOG_DIRECTORY",      TGM_DOCROOT."/logs");
 define("MESSAGE_DIRECTORY",  TGM_DOCROOT."/messages");

//================================ EMAIL ADDRESSES ==================================
 define("EMAIL_NOREPLY",      "noreply@wolftracks.org");
 define("EMAIL_DAN",          "dan@e-grainstorage.com");
 define("EMAIL_CHRIS",        "chris@tgmsystem.com");
 define("EMAIL_TIM",          "tim_wolf@webtribune.com");

 //===================================================================================
 $REMOTE_ADDR    = $_SERVER["REMOTE_ADDR"];
 $QUERY_STRING   = $_SERVER["QUERY_STRING"];
 $REQUEST_URI    = $_SERVER["REQUEST_URI"];
 $HTTP_HOST      = $_SERVER["HTTP_HOST"];
 $REQUEST_METHOD = $_SERVER["REQUEST_METHOD"];
 $DOCUMENT_ROOT  = $_SERVER["DOCUMENT_ROOT"];
 //===================================================================================
?>
