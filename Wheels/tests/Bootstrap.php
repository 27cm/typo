<?php

use Wheels\Loader;

define('DS', DIRECTORY_SEPARATOR);
define('WHEELS_TESTS_DIR', realpath(dirname(__FILE__)));
define('WHEELS_TESTS_LIBRARY_DIR', WHEELS_TESTS_DIR . DS . 'library');

require_once '..' . DS . 'library' . DS . 'Wheels.php';

Loader::staticRegister('Tests', WHEELS_TESTS_LIBRARY_DIR);
