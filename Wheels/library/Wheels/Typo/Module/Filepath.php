<?php

namespace Wheels\Typo\Module;

use Wheels\Typo\Typo;
use Wheels\Typo\Module\AbstractModule;

/**
 * Ссылки.
 *
 * Пути к файлам.
 *
 * @link http://wikipedia.org/wiki/URL
 */
class Filepath extends AbstractModule
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    static protected $_default_options = array();

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static protected $_order
        = array(
            'A' => 30,
            'B' => 0,
            'C' => 0,
            'D' => 10,
            'E' => 0,
            'F' => 0,
        );


    // --- Заменитель ---

    const REPLACER = 'FILE';


    // --- Регулярные выражения ---

    /**
     * Расширения файлов.
     *
     * @link  http://en.wikipedia.org/wiki/List_of_file_formats
     *
     * @var string[]
     */
    private $ext
        = array(
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
            'mp3',
            'mp4',
            'properties'
        );


    // --- Защищенные методы ---

    /**
     * Стадия A.
     *
     * Заменяет файлы и пути на заменитель.
     *
     * @return void
     */
    protected function stageA()
    {
        usort(
            $this->ext, function ($a, $b) {
                return strlen($b) - strlen($a);
            }
        );
        $extensionAlterations = '(' . implode('|', $this->ext) . ')';
        $windowsRestrictedSymbols = '[^' . preg_quote('<>:"/\|?*') . ']';
        $windowsPath = '(([a-z]\:)?\\\\)?(' . $windowsRestrictedSymbols . '+\\\\)*' . $windowsRestrictedSymbols . '*\.'
            . $extensionAlterations;
        $otherPaths = '/?(\w+/)*\w*\.' . $extensionAlterations;
        $this->getTypo()->getText()->preg_replace_storage(
            '~\b((' . $windowsPath . ')|(' . $otherPaths . '))(?=(\{\{\{\w+\}\}\})*\b)~iu', self::REPLACER,
            Typo::VISIBLE
        );
    }

    /**
     * Стадия D.
     *
     * Восстанавливает файлы и пути.
     *
     * @return void
     */
    protected function stageD()
    {
        $this->getTypo()->getText()->popStorage(self::REPLACER, Typo::VISIBLE);
    }
}