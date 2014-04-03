<?php

define('TEST_DIR', realpath(dirname(__FILE__)));

$root    = realpath(TEST_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
$include = $root . DIRECTORY_SEPARATOR . 'include';
set_include_path(get_include_path() . PATH_SEPARATOR . $include . PATH_SEPARATOR. $include . DIRECTORY_SEPARATOR . 'idna_convert_081');

require_once "{$root}/Wheels/library/Wheels.php";
require_once "{$root}/wheels/library/Wheels/Typo.php";
require_once TEST_DIR . '/library/Wheels/Typo/AbstractModule.php';

use Wheels\Loader;

$loader = new Loader('Wheels', TEST_DIR . DS . 'library');
$loader->register();

