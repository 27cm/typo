<?php
define('TEST_DIR', realpath(dirname(__FILE__)));

$root    = realpath(TEST_DIR . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
$include = $root . DIRECTORY_SEPARATOR . 'include';
set_include_path(get_include_path() . PATH_SEPARATOR . $include);

require_once "{$root}/Typo/library/Typo.php";
require_once "{$root}/Typo/tests/ModuleTest.php";
