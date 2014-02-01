<?php

namespace Typo\Module;

use Typo;
use Typo\Module;

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
    protected $default_options = array(
        /**
         * Используемые модули.
         *
         * @var string[]
         */
        'modules' => array('./kolobok'),
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 0,
        'B' => 0,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Заменитель ---

    const REPLACER = 'SMILE';


    // --- Защищенные методы класса ---

    /**
     * Стадия C.
     *
     * Восстанавливает файлы и пути.
     *
     * @return void
     */
    protected function stageC()
    {
        $this->text->popStorage(self::REPLACER, Typo::VISIBLE);
    }
}