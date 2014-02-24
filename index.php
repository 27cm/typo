<?php

/**
 * Typo
 *
 * Структура каталога:
 * Typo/
 *     library/         - библиотека
 *         Typo/        - вспомогательные классы
 *             Module/  - модули
 *         Typo.php     - основной класса
 *     config/          - файлы настроек
 *     tests/           - unit-тесты
 */

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL | E_STRICT);

$root    = realpath(dirname(__FILE__));
$include = $root . DIRECTORY_SEPARATOR . 'include';
set_include_path(get_include_path() . PATH_SEPARATOR . $include);

require_once "{$root}/Typo/library/Typo.php";

/**
 * \s   Пробел &#32; &#x20;     Неразрывный пробел &#160; &#xa0; &nbsp;     &#8194; &#x2002; &ensp;     &#8195; &#x2003; &emsp;
 * \h   Пробел &#32; &#x20;     Неразрывный пробел &#160; &#xa0; &nbsp;     &#8194; &#x2002; &ensp;     &#8195; &#x2003; &emsp;
 * \v   Вертикальная таб. &#11; &#xb;
 */
//for($c = 1; $c < 10000; $c++)
//{
//    if(preg_match('~\s~u', Typo\Utility::chr($c)))
//        echo $c . ' ';
//}
//echo '<br>';
//for($c = 1; $c < 10000; $c++)
//{
//    if(preg_match('~\h~u', Typo\Utility::chr($c)))
//        echo $c . ' ';
//}
//echo '<br>';
//for($c = 1; $c < 10000; $c++)
//{
//    if(preg_match('~\v~u', Typo\Utility::chr($c)))
//        echo $c . ' ';
//}
//echo '<br>';
//for($c = 1; $c < 10000; $c++)
//{
//    if(preg_match('~\t~u', Typo\Utility::chr($c)))
//        echo $c . ' ';
//}
//die();
//
//var_dump(preg_match('~\s~u', Typo\Utility::chr(11)));
//var_dump(preg_match('~\h~u', Typo\Utility::chr(11)));
//var_dump(preg_match('~\v~u', Typo\Utility::chr(11)));

if(isset($_REQUEST['text']))
    $text = $_REQUEST['text'];
else
    $text =<<<TEXT
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