<?php

use Wheels\Typo\Module\Core\Core;

return array(
    'options' => array(
        'e-convert' => array(
            'desc'    => "Замена буквы 'ё' на 'е'",
            'type'    => 'bool',
            'default' => false,
        ),
        'encoding' => array(
            'desc'    => 'Режим кодирования спецсимволов',
            'type'    => 'string',
            'aliases' => array(
                'none'     => Core::MODE_NONE,
                'names'    => Core::MODE_NAMES,
                'codes'    => Core::MODE_CODES,
                'hexcodes' => Core::MODE_HEX_CODES,
            ),
            'allowed' => array(
                Core::MODE_NONE,
                Core::MODE_NAMES,
                Core::MODE_CODES,
                Core::MODE_HEX_CODES,
            ),
            'default' => 'none',
        ),
        'html-entity-fix' => array(
            'desc'    => 'Исправление HTML-сущностей',
            'type'    => 'bool',
            'default' => true,
        ),
    ),
);
