<?php
  include("fl_common.inc");
  include("fl_jsontools.inc");

//  header("HTML/1.1 200 OK");
//  header("HTML/1.1 201 Created");
//  header("HTML/1.1 301 Moved");
    header("HTML/1.1 404 Not Found");
//  header("HTML/1.1 500 Application Error");


  $input=getRequestInput();
  $input->fullname=$input->firstname." ".$input->lastname;

  $input->method=$_SERVER["REQUEST_METHOD"];
  $input->methodOverride=$_SERVER["HTTP_X_METHOD_OVERRIDE"];

  $ifNoneMatch="";
  $requestHeaders=getallheaders();
  if (isset($requestHeaders["If-None-Match"]))
    $ifNoneMatch=$requestHeaders["If-None-Match"];

  $input->ifNoneMatch=$ifNoneMatch;

  sendData($input);
?>
