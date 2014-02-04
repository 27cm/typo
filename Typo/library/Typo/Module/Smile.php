<?php

namespace Typo\Module;

use Typo;
use Typo\Module;
use Typo\Utility;

/**
 * Смайлы.
 *
 * @link http://en.wikipedia.org/wiki/Smiley
 * @link http://en.wikipedia.org/wiki/Emoticon
 */
class Smile extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array();

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 30,
        'B' => 0,
        'C' => 5,
        'D' => 0,
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

    /**
     * Расширение файлов изображений.
     *
     * @var string
     */
    static public $ext = '';


    // --- Заменитель ---

    const REPLACER = 'SMILE';


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
            return $_this->text->pushStorage($data, Smile::REPLACER, Typo::VISIBLE);
        };

        $smiles = array_map('preg_quote', array_keys($class::$smiles));

        $pattern = '~(?<!{p})(?:' . implode('|', $smiles) . ')(?!{p})~u';
        self::pregHelpers($pattern);

        $this->typo->text->preg_replace_callback($pattern, $callback);
    }

    /**
     * Стадия C.
     *
     * Восстанавливает смайлики.
     *
     * @return void
     */
    protected function stageC()
    {
        if($this->typo->options['html-out-enabled'])
        {
            $this->text->popStorage(self::REPLACER, Typo::VISIBLE);
        }
    }
}