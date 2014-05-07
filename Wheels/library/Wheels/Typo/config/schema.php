<?php

return array(
    'options' => array(
        'charset'          => array(
            'desc'    => 'Кодировка текста',
            'type'    => '\Wheels\Typo\Type\Tcharset',
            'default' => 'UTF-8',
        ),
        'encoding'         => array(
            'desc'    => 'Режим кодирования спецсимволов',
            'type'    => 'string',
            'aliases' => array(
                'none'     => self::MODE_NONE,
                'names'    => self::MODE_NAMES,
                'codes'    => self::MODE_CODES,
                'hexcodes' => self::MODE_HEX_CODES,
            ),
            'allowed' => array(
                self::MODE_NONE,
                self::MODE_NAMES,
                self::MODE_CODES,
                self::MODE_HEX_CODES,
            ),
            'default' => 'none',
        ),
        'html-in-enabled'  => array(
            'desc'    => 'Включение HTML в тексте на входе',
            'type'    => 'bool',
            'default' => true,
        ),
        'html-out-enabled' => array(
            'desc'    => 'Включение HTML в тексте на выходе',
            'type'    => 'bool',
            'default' => true,
        ),
        'modules'          => array(
            'default' => array('html', 'nobr', 'punct', 'space', 'symbol', 'url'),
        ),
        'e-convert'        => array(
            'desc'    => "Замена буквы 'ё' на 'е'",
            'type'    => 'bool',
            'default' => false,
        ),
    ),
);
