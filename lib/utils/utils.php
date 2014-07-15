<?php

function base64_encode_file($filename)
{
    $encoded = '';
    if ($filename && ($fh=fopen($filename, "r"))) {
        $bin = fread($fh, filesize($filename));
        $encoded = base64_encode($bin);
        fclose($fh);
    }
    return $encoded;
}

/**
 * @param string $guid
 * @return bool False on failure
 */
function is_guid($guid)
{
    return strlen($guid) == 36 && preg_match("/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/i", $guid);

}

/**
 * @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
 */
function create_guid()
{
    $microTime = microtime();
    list($a_dec, $a_sec) = explode(" ", $microTime);

    $dec_hex = dechex($a_dec* 1000000);
    $sec_hex = dechex($a_sec);

    ensure_length($dec_hex, 5);
    ensure_length($sec_hex, 6);

    $guid = "";
    $guid .= $dec_hex;
    $guid .= create_guid_section(3);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= create_guid_section(4);
    $guid .= '-';
    $guid .= $sec_hex;
    $guid .= create_guid_section(6);

    return $guid;

}

function GUID()
{
    return create_guid();
}

function create_guid_section($characters)
{
    $return = "";
    for ($i=0; $i<$characters; $i++) {
        $return .= dechex(mt_rand(0,15));
    }

    return $return;
}


function ensure_length(&$string, $length)
{
    $strlen = strlen($string);
    if ($strlen < $length) {
        $string = str_pad($string,$length,"0");
    } elseif ($strlen > $length) {
        $string = substr($string, 0, $length);
    }
}


function translate($string, $selectedValue='')
{
    $returnValue = '';
    global $app_strings;

    if (isset($app_strings[$string])) {
        $returnValue = $app_strings[$string];
    }

    if (empty($returnValue)) {
        return $string;
    }

    if (is_array($returnValue) && (!empty($selectedValue) || (is_numeric($selectedValue) && $selectedValue == 0))  && isset($returnValue[$selectedValue]) ) {
        return $returnValue[$selectedValue];
    }

    return $returnValue;
}

/**
 * Hash a plaintext password for use with our APIs
 *
 * @param string $password    required=true
 * @param string $username    required=true
 * @return string
 */
function hashApiPassword($password, $username)
{
    $md5Password = md5($password);
    return hashPassword($md5Password, $username);
}

/**
 * The one function for hashing a plaintext password
 * @param string  $password  The plaintext password
 * @param string  $username  The username (becomes part of the hash)
 * @param boolean $noSalt    If true, hash will NOT be salted
 * @return string   A hex encoded string representation of the hash
 */
function hashPassword($password, $username, $noSalt=false)
{
    if($username=='') {
        Log::$l->error("hashPassword() called with no username. " . nice_backtrace(true));
    }
    if(!Config::get('security.pwhash')) {
        $noSalt = true;
    }
    if($noSalt) {
        $salted = $password;
    } else {
        $salt1 = md5($username, true);
        $salt2 = 'llbB3U49BwSWd0EB!?cn9TKDsqHHo0k3';
        $salted = $salt2 . $password . $salt1;
    }
    $hash = hash('sha512', $salted);
    return $hash;
}

/**
 * Generates a backtrace without all the nasty recursive args
 * @param $bString True if you want the result as a string, false for an array
 * @return mixed An array or a string
 */
function nice_backtrace($bString=false, $oException=null)
{
    if( $oException )
        $bt = $oException->getTrace();
    else
        $bt = debug_backtrace();
    foreach( $bt as $num=>$call ) {
        unset( $bt[$num]['args'] );
        unset( $bt[$num]['object'] );
    }
    if( $bString ) return print_r( $bt, true );
    return $bt;
}

/**
 * Returns a string of comma separated question marks, one for each element of $input
 *
 * @param array $input
 * @return string
 */
function sqlPlaceholder( &$input )
{
    if(count($input)==0) return "";
    $result = "(" . str_repeat("?,", count($input)-1) . "?)";
    return $result;
}

