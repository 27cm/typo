<?php

use Wheels\Loader;

require_once '../library/Wheels.php';

define('TESTS_DIR', realpath(dirname(__FILE__)));
define('TESTS_CONFIG_DIR', dirname(__FILE__) . DS . 'config');
define('TESTS_LIB_DIR', dirname(__FILE__) . DS . 'library');
define('TESTS_RES_DIR', dirname(__FILE__) . DS . 'resources');

Loader::staticRegister('Tests', TESTS_LIB_DIR);
