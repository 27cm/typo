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
    static public $smiles = array(
        ':)'  => 'smile3',
        ':-)' => 'smile3',
        '=)'  => 'smile3',
        /* ... */
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
            // ...
            return $_this->text->pushStorage($data, Smile::REPLACER); // return [[[SMILE1]]]
        };

        // ...

        // @see \Typo\Module\Url::stageA()
        // :-)
        // <img alt=":-)" title=":-)" src="http://www.kolobok.us/smiles/standart/smile3.gif">
        // Но всместо $this->typo->text->preg_replace_callback($pattern, $callback);
        // написать и использовать $this->typo->text->replace($search, $callback);
    }
}