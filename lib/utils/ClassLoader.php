<?php

class ClassLoader
{
    /**
     * Useful for injecting Test Mock Class Instances into Test Environment
     * @param array $mockInstances - Class to Instance Map
     */
    private static $mockInstances = array();

    /**
     * @param array - Well Known Include Directories
     */
    private static $dirMap = array(
        "lib/",
    );

    /**
     * @return array $classMap - Class to filename map
     */
    private static function getClassMap()
    {
        return array(
            "JobQueue"  =>  Config::get('jobqueue.path'),
        );
    }

    public static function autoload($className)
    {
        $classMap = self::getClassMap();
        if (!empty($classMap[$className])) {
            $file = $classMap[$className];
            if (file_exists($file)) {
                // printf("Autload ClassMap - Class: %s  File: %s\n", $className, $file);
                include_once("$file");
                return true;
            }
        }
        foreach(self::$dirMap as $dir) {
            if (file_exists("{$dir}$className.php")) {
                include_once("{$dir}$className.php");
                return true;
            }
        }
        return false;
    }


    public static function getInstance($className)
    {
        if (!empty(self::$mockInstances[$className])) {
            return self::$mockInstances[$className];
        }
        return new $className();
    }

    public static function addMockInstance($className, $obj)
    {
        self::$mockInstances[$className] = $obj;
    }

    public static function removeMockInstance($className)
    {
        unset(self::$mockInstances[$className]);
    }

    public static function clearMockInstances()
    {
        self::$mockInstances = array();
    }

}

spl_autoload_register(array('ClassLoader', 'autoload'));

