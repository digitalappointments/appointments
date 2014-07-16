<?php
require_once(dirname(__FILE__) . "/../../lib/env/bootstrap.php");
define('ENTRY_POINT_TYPE', 'test');

$fileName = 'FieldDefinitions.php';

// include_once("$fileName");
// print_r(MODEL::$definitions);
// exit;

$tables = array();
$sql = "SHOW TABLES";
// printf("\n%s\n",$sql);
$result = D::$dbm->query($sql);
while($row = D::$dbm->fetchArray($result)) {
    foreach($row as $k=>$tableName) {
        $tables[$tableName] = in_array($tableName, Model::$tables);
    }
}

// var_export($tables);

$output = "";
$output .= sprintf ("<" . "?" . "php\n");

$model = array();
foreach($tables AS $tableName => $active) {
    $model[$tableName] = getFieldDefinitions($tableName);
    if ($active) {
        $output .= sprintf ("    \n//------ Table: %s ------\n", $tableName);
        $output .= sprintf ("Model::" . "\$" . "definitions['" . $tableName  . "'] = array(\n");

            // $output .= var_export($model[$tableName],true);

            foreach($model[$tableName] AS $fieldName => $fieldInfo) {
                $output .= sprintf ("    '%s' => ", $fieldName);
                    // $output .= var_export($model[$tableName][$fieldName],true);
                    $s = var_export($model[$tableName][$fieldName],true);
                    $s = substr($s,0,strlen($s)-1);
                    $output .= $s;
                $output .= sprintf ("    ),\n");
            }

        $output .= sprintf (");\n\n");
    }
}
echo $output;


$bytesWritten = file_put_contents($fileName, $output);

printf("Bytes = %d\n",$bytesWritten);
exit;



function getFieldDefinitions($tableName)
{
    $fields = array();
    $sql = "DESCRIBE " . $tableName;
    $result = D::$dbm->query($sql);
    while($row = D::$dbm->fetchArray($result)) {
        $fieldName = $row['Field'];
        $info = array();

        $t=explode('(', $row['Type']);
        if ($fieldName == 'id') {
            $info['type'] = 'guid';
            $info['key'] = true;
        } else {
            $info['type'] = $t[0];
        }
        if (count($t)>1) {
            $info['len'] = (int) $t[1];
        }
        $info['api'] = true;

        $fields[$fieldName] = $info;
    }
    return $fields;
}


/**
$sql = "DESCRIBE " . $tableName;
printf("\n%s\n",$sql);
$result = D::$dbm->query($sql);
while($row = D::$dbm->fetchArray($result)) {
    $fieldName = $row['Field'];
    $info = array();

    $t=explode('(', $row['Type']);
    //print_r($t);

    if ($fieldName == 'id') {
        $info['type'] = 'guid';
        $info['key'] = true;
    } else {
        $info['type'] = $t[0];
    }
    if (count($t)>1) {
       $info['len'] = (int) $t[1];
    }
    $info['api'] = true;

    $fields[$fieldName] = $info;
}
//print_r($fields);


var_export($fields);

**/

//
//class Model {
//    public static $tables = array(
//        'accounts',
//        'clients',
//        'users',
//    );
//    public static $definitions;
//}

