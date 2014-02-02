<?php

namespace Typo\Module\Smile\Kolobok;

use Typo\Module\Smile;
use Typo\Module\Smile\Kolobok;

/**
 * Стандартные "колобки".
 *
 * @link http://www.kolobok.us/content_plugins/gallery/gallery.php?smiles.2.1
 */
class Standart extends Kolobok
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array();

    static public $url = 'standart/';

    /**
     * Смайлики.
     *
     * @var array
     */
    public $smiles = array(
        ':)'  => 'smile3',
        ':-)' => 'smile3',
        '=)'  => 'smile3',
        ':('  => 'sad',
        ':-(' => 'sad',
        '=('  => 'sad',
        '=D'  => 'rofl',
        ':D'  => 'rofl',
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
        $_this = $this;

        $callback = function($search) use($_this)
        {
            $data = "<img alt='{$search}' title='{$search}' src='http://www.kolobok.us/smiles/standart/{$_this->smiles[$search]}.gif'>";
            return $_this->text->pushStorage($data, Smile::REPLACER); 
        };
        $this->typo->text->replace_callback(array_keys($this->smiles), $callback);
    }
}