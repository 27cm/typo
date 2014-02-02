<?php

namespace Typo\Module;

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

    const REPLACER = '[[[FILE%u]]]';

    // --- Регулярные выражения ---
    /** Файловый разделитель */
    const DELIMETER = '[\\\\/]';

    private $extensions = array(
        // Текст
        'txt',
        // Изображения
        'jpg',
        'jpeg',
        'gif',
        'bmp',
        'png',
        // Исполняемые файлы
        'exe',
        'bat',
        // MS Office
        'doc',
        'docx',
        'xls',
        'xlsx',
        // Аудио
        'mp3',
        'wav',
        'mid',
        // Видео
        'mp4',
        'avi',
        'mov',
        'wmv',
        'vlc',
        // Исходные коды
        'htm', // HTML
        'java', // Java
        'class',
        'php', // PHP
        'cpp', // C++
        'py', // Pyhton
        'hs', // Haskell
        'cs', //C#
        'sln',
        'suo',
        // Разные
        'djvu',
        'conf',
        'ini',
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
        usort($this->extensions,function ($a,$b){ return strlen($b)-strlen($a);});
        $extensionAlterations = '(' . implode('|',$this->extensions) . ')';
        $windowsRestrictedSymbols = '[^' . preg_quote('<>:"/\|?*') . ']';
        $pattern = '~(([A-Z]\:(?=\\\\))?' . self::DELIMETER . ')?(' . $windowsRestrictedSymbols . '+' . self::DELIMETER . ')*' . $windowsRestrictedSymbols . '*\.'. $extensionAlterations . '\b~u';
        $this->text->preg_replace_storage($pattern, self::REPLACER);
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
        $this->text->popStorage(self::REPLACER);
    }
}