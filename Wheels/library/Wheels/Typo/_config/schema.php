<?php

use Wheels\Typo\Typo;

return array(
    'options' => array(
        'charset' => array(
            'desc'    => 'Кодировка текста',
            'type'    => '\Wheels\Typo\Type\Tcharset',
            'default' => 'UTF-8',
        ),
        'encoding' => array(
            'desc'    => 'Режим кодирования спецсимволов',
            'type'    => 'string',
            'aliases' => array(
                'none'     => Typo::MODE_NONE,
                'names'    => Typo::MODE_NAMES,
                'codes'    => Typo::MODE_CODES,
                'hexcodes' => Typo::MODE_HEX_CODES,
            ),
            'allowed' => array(
                Typo::MODE_NONE,
                Typo::MODE_NAMES,
                Typo::MODE_CODES,
                Typo::MODE_HEX_CODES,
            ),
            'default' => 'none',
        ),
        'html-in-enabled' => array(
            'desc'    => 'Включение HTML в тексте на входе',
            'type'    => 'bool',
            'default' => true,
        ),
        'html-out-enabled' => array(
            'desc'    => 'Включение HTML в тексте на выходе',
            'type'    => 'bool',
            'default' => true,
        ),
        'e-convert' => array(
            'desc'    => "Замена буквы 'ё' на 'е'",
            'type'    => 'bool',
            'default' => false,
        ),
        'modules' => array(
            'desc'    => 'Используемые модули',
            'type'    => '\Wheels\Typo\Type\Tmodule[]',
            'default' => array('core', 'html', 'nobr', 'punct', 'space', 'symbol', 'url'),
        ),
    ),
);
