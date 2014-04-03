<?php

// @link http://daringfireball.net/projects/markdown/

error_reporting(~E_ALL);

define('DS', DIRECTORY_SEPARATOR);

require_once 'PEAR.php';
require_once 'File.php';
require_once 'File' . DS . 'Util.php';

$root = realpath(dirname(__FILE__) . DS . '..');
$filename = $root . DS . 'README.md';

$version = '0.3';
$title = 'Wheels\\Typo &ndash; типограф (ver. ' . $version . ')';
// $underline = str_repeat('=', mb_strlen($title));

$data = <<<"README"
$title
$underline

## Возможности

README;

function test($dir, &$data)
{
    $entries = File_Util::listDir($dir, FILE_LIST_FILES);
    foreach($entries as $entrie)
    {
        $file = new File();
        $contents = $file->readAll($dir . DS . $entrie->name);

        $matches = array();
        preg_match_all('~#[ABCDEF]\d+\h+(?<rule>[^\h\r\n][^\r\n]+)~u', $contents, $matches);
        if(!empty($matches['rule']))
            $data .= "\r\n{$entrie->name}:\r\n";
        foreach($matches['rule'] as $rule)
        {
            $data .= '* ' . addcslashes($rule, '\\`*_{}[]()#+-.!') . "\r\n";
        }
    }

    $entries = File_Util::listDir($dir, FILE_LIST_DIRS);
    foreach($entries as $entrie)
    {
        test($dir . DS . $entrie->name, $data);
    }
}

test($root . DS . 'Wheels' . DS . 'library' . DS . 'Wheels', $data);

$file = new File();

$e = $file->write($filename, $data, FILE_MODE_WRITE);
if(PEAR::isError($e))
    echo 'Could not write to file: ' . $e->getMessage();

$file->closeAll();
