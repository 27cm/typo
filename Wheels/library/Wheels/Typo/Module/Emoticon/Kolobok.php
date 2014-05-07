<?php

namespace Wheels\Typo\Module\Smile;

use Wheels\Typo\Module\Emoticon;

/**
 * Смайлы "Колобки".
 *
 * @link http://www.kolobok.us/
 */
class Kolobok extends Emoticon
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    static protected $_default_options
        = array(
            /**
             * Используемые модули.
             *
             * @var string[]
             */
            'modules' => array(
                'emoticon/kolobok/standart',
            ),
        );

    /**
     * Расширение файлов изображений.
     *
     * @var string
     */
    static public $ext = 'gif';
}