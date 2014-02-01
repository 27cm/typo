<?php

namespace Typo\Module\Smile;

use Typo\Module\Smile;

/**
 * Смайлы "Колобки".
 *
 * @link http://www.kolobok.us/
 */
class Kolobok extends Smile
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
        'modules' => array('./standart'),
    );

    static public $url = 'http://www.kolobok.us/smiles/';

    static public $ext = 'gif';
}