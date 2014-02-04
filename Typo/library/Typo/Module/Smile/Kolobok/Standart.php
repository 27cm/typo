<?php

namespace Typo\Module\Smile\Kolobok;

use Typo\Module\Smile\Kolobok;

/**
 * Стандартные "колобки".
 *
 * @link http://www.kolobok.us/content_plugins/gallery/gallery.php?smiles.2.1
 */
class Standart extends Kolobok
{
    /**
     * URL изображений.
     *
     * @var string
     */
    static public $url = 'http://www.kolobok.us/smiles/standart/';

    /**
     * Смайлики.
     *
     * @var array
     */
    static public $smiles = array(
        ':)'  => 'smile3',
        ':-)' => 'smile3',
        '=)'  => 'smile3',
        ':('  => 'sad',
        ':-(' => 'sad',
        '=('  => 'sad',
        '=D'  => 'rofl',
        ':D'  => 'rofl',
    );
}