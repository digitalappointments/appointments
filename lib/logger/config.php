<?php

function directoryEndsWith($findPath, $dir)
{
    $rootDir = $dir;
    $pos = strpos($dir, $findPath);
    if ($pos !== false) {
        $rootDir = substr($dir, 0, $pos + strlen($findPath));
    }
    return $rootDir;
}

if (empty($GLOBALS['logger_file_name'])) {
    $loggerFileName = 'http.log';
} else {
    $loggerFileName = $GLOBALS['logger_file_name'];
}

$rootDir = directoryEndsWith('/vendor/', __DIR__) . '..';

// var_dump($rootDir);
// $f =   "{$rootDir}/log/{$loggerFileName}";
// var_dump($f);
// exit;

return array(
    'rootLogger' => array(
        // 'DEBUG', 'INFO', 'WARN', 'ERROR', 'FATAL'
        'level' => 'INFO',
        'appenders' => array('default'),
    ),
    'appenders' => array(
        'default' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutPattern',
                'params' => array(
                    'conversionPattern' => '%date [%level] %message%newline',
                ),
            ),
            'params' => array(
                'file' => "{$rootDir}/log/{$loggerFileName}",
                'append' => true,
            ),
        ),
    ),
);
