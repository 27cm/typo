<?php

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL | E_STRICT);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

$root    = realpath(dirname(__FILE__) . DS . '..');
$include = $root . DS . 'include';
set_include_path(get_include_path() . PS . $include . PS . $include . DS . 'idna_convert_081');

if(isset($_REQUEST['text']))
    $input = $_REQUEST['text'];
else
    $input =<<<TEXT
    ё Ё
    0. §
    1. &sect
    2. &sect;
    3. &#X00A7;
    4. &#x00a7;
    5. &#xA7
    6. &#xA7;
    7. &#xa7;
    8. &#167
    9. &#167;
    10. &#0167;
    11. &#000167;
    12. &#189256756767;
    12. &#xA7 ;
TEXT;

use Wheels\Typo\Typo;
use Wheels\Diff;

try
{
    require_once $root . DS . 'Wheels' . DS . 'library' . DS . 'Wheels.php';


//    setlocale(LC_ALL, 'ru_RU');
//    for($i = 0; $i < 65000; $i++)
//        if(preg_match('~[\x00-\xFF]~', Typo\Utility::chr($i)))
//            echo $i . ' = ' . Typo\Utility::chr($i) . '<br>';
//    die();


//
//    die();
//
//
//
//    $type = $option->getType();
//    die(get_class($type));

    $typo = new Typo();

    $options = array('modules' => array('core', 'html', 'emoticon/skype'));
    $output = $typo->process($input, $options);

    $diff = new Diff($input, $output);
}
catch(Exception $e) {
    $error = $e->getMessage();
    die($error);
}

include 'template.php';