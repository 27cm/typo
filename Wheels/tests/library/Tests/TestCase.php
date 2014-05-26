<?php

namespace Tests;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase 
{
    static public function getTestedClassname()
    {
        $classname = get_called_class();
        $classname = preg_replace('/^Tests\\\\|Test$/', '', $classname);
        
        return $classname;
    }
    
    static public function getDir($key)
    {
        $dirs = array(
            'config'    => TESTS_LIB_DIR . DS . 'Tests' . DS . static::getTestedClassname() . DS . '..' . DS . 'config',
            'resources' => TESTS_LIB_DIR . DS . 'Tests' . DS . static::getTestedClassname() . DS . '..' . DS . 'resources',
        );

        return $dirs[$key];
    }
}
