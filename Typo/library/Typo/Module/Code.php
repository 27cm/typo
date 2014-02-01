<?php

namespace Typo\Plugins;

use Typo\Text;

function code(Text $text)
{
    // Добавить проверку опций html-in.enabled = true и html-out.enabled = true
    $callback = function($matches)
    {
        // Оборачиваем в <pre>...</pre>
        if($matches[1] === '');
            $matches[0] = "<pre>{$matches[0]}</pre>";

        $replace = array(
            // Оборачиванием каждую строку в <code>...</code>
            '~\r?\n~s' => "</code>\n<code{$matches[2]}>",

            // Убираем висячие пробелы
            '~\h+(?=</code>)~' => '',

            // Заменяем табуляцию на 4 пробела
            '~\t~' => '    ',
        );

        return preg_replace(array_keys($replace), array_values($replace), $matches[0]);
    };

    $text->preg_replace_callback('~(<pre[^>]*>\s*)?<code([^>]*)>.*\n.*<\/code>~isU', $callback);
}