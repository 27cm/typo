<?php

namespace Typo\Module;

use Typo;
use Typo\Module;

/**
 * Ссылки.
 *
 * Пути к файлам.
 *
 * @link http://wikipedia.org/wiki/URL
 */
class Filepath extends Module
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
        'A' => 0,
        'B' => 0,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Заменитель ---

    const REPLACER = 'FILE';


    // --- Защищенные методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет файлы и пути на заменитель.
     *
     * @return void
     */
    protected function stageA()
    {
        //
        // $this->text->preg_replace_storage($pattern, self::REPLACER);
    }

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