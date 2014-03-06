<?php

namespace Wheels\Typo\Module;

use Wheels\Typo;
use Wheels\Typo\Module;
use Wheels\Typo\Utility;

/**
 * Эмотиконы (смайлики).
 *
 * Заменяет эмотиконы на html теги изображений.
 *
 * @link http://en.wikipedia.org/wiki/Emoticon
 */
class Emoticon extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array(
        /**
         * Используемые модули.
         *
         * @var string[]
         */
        'modules' => array(
            'emoticon/kolobok',
        ),
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 35,
        'B' => 0,
        'C' => 0,
        'D' => 5,
        'E' => 0,
        'F' => 0,
    );

    /**
     * Смайлики.
     *
     * @var array
     */
    static public $smiles = array();



    /**
     * URL изображений.
     *
     * @var string
     */
    static public $url = '';


    // --- Заменитель ---

    const REPLACER = 'EMOTICON';


    // --- Защищенные методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет смайлики на заменитель.
     *
     * @return void
     */
    protected function stageA()
    {
        if(!$this->typo->options['html-out-enabled'])
            return;

        $class = get_called_class();
        if(empty($class::$smiles))
            return;

        $_this = $this;

        $callback = function($matches) use($_this, $class)
        {
            $smile = $matches[0];

            $attrs = array(
                'src' => $class::$url . $class::$smiles[$smile] . '.' . $class::$ext,
                'title' => $smile,
                'alt' => $smile,
            );
            $data = Utility::createElement('img', null, $attrs);
            return $_this->text->pushStorage($data, Emoticon::REPLACER, Typo::VISIBLE);
        };

        $smiles = array_map('preg_quote', array_keys($class::$smiles));

        $pattern = '~(?<!{p})(?:' . implode('|', $smiles) . ')(?!{p})~u';
        self::pregHelpers($pattern);

        $this->typo->text->preg_replace_callback($pattern, $callback);
    }

    /**
     * Стадия D.
     *
     * Восстанавливает смайлики.
     *
     * @return void
     */
    protected function stageD()
    {
        if($this->typo->options['html-out-enabled'])
        {
            $this->text->popStorage(self::REPLACER, Typo::VISIBLE);
        }
    }
}