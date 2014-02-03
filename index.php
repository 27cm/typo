<?php

/**
 * Typo
 *
 * Структура каталога:
 * Typo/
 *     library/         - библиотека
 *         Typo/        - вспомогательные классы
 *             Modules/ - модули
 *         Typo.php     - основной класса
 *     config/          - файлы настроек
 *     tests/           - unit-тесты
 */

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL | E_STRICT);

$root    = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$include = "{$root}/include";
set_include_path(get_include_path() . PATH_SEPARATOR . $include);

require_once "{$root}/Typo/library/Typo.php";

if(isset($_REQUEST['text']))
    $text = $_REQUEST['text'];
else
    $text =<<<TEXT
TEXT;

try
{
    $typo = new Typo();
    $executed_text = $typo->execute($text);
}
catch(\Typo\Exception $e)
{
    echo $e->getMessage() . '<br>';

    while($e->hasPrevious())
    {
        $e = $e->getPrevious();
        echo $e->getMessage() . '<br>';
    }
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
    <textarea name="text" rows="10" style="width: 100%;"><?php if(isset($text)) echo htmlentities($text, ENT_QUOTES, 'utf-8'); ?></textarea>
    <input type="submit" value="ОТТИПОГРАФИРОВАТЬ" style="width: 100%; height: 40px;">
</form>
<div style="margin: 10px; padding: 15px 30px; border: 1px gray solid; font-size: 1.7em;"><?php if(isset($executed_text)) echo $executed_text; ?></div>