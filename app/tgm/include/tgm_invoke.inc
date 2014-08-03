<?php

function trim_EOL($s)
  {
    $len=strlen($s);
    if ($len>0)
      {
        if (substr($s,$len-1,1) == "\r" ||
            (substr($s,$len-1,1) == "\n"))
          $s=substr($s,0,$len-1);
      }
    $len=strlen($s);
    if ($len>0)
      {
        if (substr($s,$len-1,1) == "\r" ||
            (substr($s,$len-1,1) == "\n"))
          $s=substr($s,0,$len-1);
      }
    $len=strlen($s);
    return($s);
  }


function invokeService($host,$path,$data)
  {
    $LINETRACE=FALSE;
    $outbound="";
    $first=TRUE;
    $status=0;

    if (!isset($data) || is_null($data))
      $outbound="";
    else
    if (is_string($data))
      $outbound=$data;
    else
    if (is_array($data))
      {
        foreach($data AS $k=>$v)
          {
            if ($first)
              $first=FALSE;
            else
              $outbound .= "&";
            $outbound .= $k."=".urlencode($v);
          }
      }

    $fp = fsockopen($host, 80, $errno, $errstr, 30);
    if (!$fp) return array(99,"(ERROR: $errno) $errstr");

    $vbuf  = "POST $path HTTP/1.0\n";
    $vbuf .= "Host: $host\n";
    $vbuf .= "Content-type: application/x-www-form-urlencoded\n";
    $vbuf .= "Content-length: " . strlen($outbound) . "\n";
    $vbuf .= "Connection: close\n\n";
    $vbuf .= $outbound;
    if ($LINETRACE)
      {
        echo $vbuf."\n";
      }
    fputs($fp, $vbuf);

    $headers=TRUE;
    $buf="";
    while (!feof($fp))
      {
        $buffer = trim_EOL(fgets($fp,8192));
        if ($LINETRACE)
          {
            echo $buffer."\n";
          }
        if ($headers)
          {
             if ($buffer=="\r\n" || $buffer=="")
               $headers=FALSE;
             else
               {
                 if (ereg("^HTTP", $buffer))
                   {   //came to the start of the header - Get Status
                     $status=500;
                     list($http, $status_code, $statustext) = split(" ", $buffer);
                     $s="";
                     for ($i=0; $i<strlen($status_code); $i++)
                       {
                         $temp=substr($status_code,$i,1);
                         if ($temp >= "0" && $temp <= "9")
                            $s .= $temp;
                       }
                     if (strlen($s) > 0)
                       $status=(int) $s;
                   }
               }
          }
        else
          {
             if ($buffer!="\r\n")
               $buf .= $buffer;
          }
      }
    fclose($fp);
    if ($status != 200)
      {
        return(array($status, $buf));
      }
    return(array(0,$buf));
  }
?>
