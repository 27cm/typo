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


    // --- Регулярные выражения ---

    /** Файловый разделитель */
    const DELIMETER = '[\\\\/]';

    /**
     * Расширения файлов.
     *
     * @link  http://en.wikipedia.org/wiki/List_of_file_formats
     *
     * @var string[]
     */
    private $ext = array(
        '[a-z]{2,3}',
        'jpeg',
        'docx',
        'xlsx',
        'pptx',
        'html',
        'java',
        'class',
        'djvu',
        'conf',
    );


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
        usort($this->ext,function ($a,$b){ return strlen($b)-strlen($a);});
        $extensionAlterations = '(' . implode('|', $this->ext) . ')';
        $windowsRestrictedSymbols = '[^' . preg_quote('<>:"/\|?*') . ']';
        $pattern = '~(([a-z]\:(?=\\\\))?' . self::DELIMETER . ')?(' . $windowsRestrictedSymbols . '+' . self::DELIMETER . ')*' . $windowsRestrictedSymbols . '*\.'. $extensionAlterations . '\b~iu';
        $this->text->preg_replace_storage($pattern, self::REPLACER, Typo::VISIBLE);
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