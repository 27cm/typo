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

//    $diff = new Diff('slovo', 'olovo');
//
//    echo $diff->renderDiffToHTML();
//
//    die();
//
//
//
//    $type = $option->getType();
//    die(get_class($type));

    $options = array('charset' => 'Windows-1251');
    $typo = new Typo($options);

    $typo->getModule('html')->setOption('paragraphs', false);

    $typo->setConfigDir($root . '/config/wheels/typo');
//    $typo->setOptionsFromGroup('comment');



//    echo '<pre>';
//    var_dump($typo->getModules());
//    die();

//    $typo->setConfigDir($root . DS . 'Wheels' . DS . 'tests' . DS . 'config' . DS . 'Module' . DS . 'Punct' . DS . 'QuoteTest');
//    $typo->setOptions('default');
    //$typo->addModule('emoticon/skype');
    // var_dump($typo);
    //$output = $typo->process($input);
}
catch(Wheels\Typo\Exception $e)
{
    echo $e->getMessage() . '<br>';

    while($e->hasPrevious())
    {
        $e = $e->getPrevious();
        echo $e->getMessage() . '<br>';
    }
}
catch(Exception $e)
{
    echo $e->getMessage() . '<br>';
}

?>
<style>
    div
    {
        /*line-height: 1.4em;*/
    }

    a
    {
        display: inline-block;
        padding: 0;
        margin: 0;
        text-decoration: none;
        color: #3366ff;
        border-bottom: 1px #3366ff dotted;
    }
</style>
<form method="post">
    <textarea name="text" rows="10" style="width: 100%;"><?php if(isset($input)) echo htmlspecialchars($input, ENT_QUOTES, 'utf-8'); ?></textarea>
    <input type="submit" value="ОТТИПОГРАФИРОВАТЬ" style="width: 100%; height: 40px;">
</form>
<div style="margin: 10px; padding: 15px 30px; border: 1px gray solid; font-size: 1.7em;"><?php if(isset($output)) echo $output; ?></div>