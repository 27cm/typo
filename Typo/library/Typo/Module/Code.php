<?php

namespace Typo\Module;

use Typo\Text;


/**
 * HTML.
 *
 * Заменяет все теги и указанные блоки на безопасные конструкции, а затем восстанавливает их.
 *
 * @link http://wikipedia.org/wiki/HTML
 */
class Code extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array(
        /**
         * Безопасные блоки, содержимое которых не обрабатывается типографом.
         *
         * @var string|string[]
         */
        'safe-blocks' => array('<!-- -->', 'code', 'comment', 'pre', 'script', 'style'),
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 5,
        'B' => 0,
        'C' => 30,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );

    /**
     * Невидимые HTML блоки.
     *
     * @var array
     */
    static $invisible_blocks = array('<!-- -->', 'comment', 'head', 'script', 'style');

    /**
     * Видимые HTML теги.
     *
     * @var array
     */
    static $visible_tags = array('img', 'input');


    // --- Защищенные методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет безопасные блоки и теги на соответствующие заменители.
     *
     * @return void
     */
    protected function stageA()
    {
        if(!$this->typo->options['html-in-enabled'] || !$this->typo->options['html-out-enabled'])
            return;

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
}