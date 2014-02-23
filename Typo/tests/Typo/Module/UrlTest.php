<?php
$root    = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' ;
$include = "{$root}/include";
set_include_path(get_include_path() . PATH_SEPARATOR . $include);
require_once "{$root}/ModuleTest.php";


class UrlTest  extends ModuleTest {
    public function testUrl1() {
    }
}