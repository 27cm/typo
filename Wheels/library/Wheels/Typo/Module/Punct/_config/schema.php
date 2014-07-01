<?php

use Wheels\Typo\Module\Punct\Punct;

return array(
    'options' => array(
        'normalize' => array(
            'desc'    => 'Нормализация пунктуации',
            'type'    => 'bool',
            'default' => true,
        ),
        'quotes' => array(
            'desc'    => 'Расстановка кавычек',
            'type'    => 'bool',
            'default' => true,
        ),
        'quote-open' => array(
            'desc'    => 'Открывающая кавычка',
            'type'    => 'Wheels\Typo\Type\Tentity',
            'default' => 'laquo',
//                       'ldquo' (американский английский)
//                       'lsquo' (британский английский)
        ),
        'quote-close' => array(
            'desc'    => 'Закрывающая кавычка',
            'type'    => 'Wheels\Typo\Type\Tentity',
            'default' => 'raquo',
//                       'rdquo' (американский английский)
//                       'rsquo' (британский английский)
        ),
        'subquote-open' => array(
            'desc'    => 'Внутренняя открывающая кавычка',
            'type'    => 'Wheels\Typo\Type\Tentity',
            'default' => 'bdquo',
//                       'lsquo' (американский английский)
//                       'ldquo' (британский английский)
        ),
        'subquote-close' => array(
            'desc'    => 'Внутренняя закрывающая кавычка',
            'type'    => 'Wheels\Typo\Type\Tentity',
            'default' => 'ldquo',
//                       'rsquo' (американский английский)
//                       'rdquo' (британский английский)
        ),
        'hyphenation' => array(
            'desc'    => 'Расстановка мягких переносов (мест возможного переноса) в словах',
            'type'    => 'string',
            'aliases' => array(
                'none' => Punct::HYPEN_NONE,
                'shy'  => Punct::HYPEN_SHY,
                'wbr'  => Punct::HYPEN_WBR,
            ),
            'allowed' => array(
                Punct::HYPEN_NONE,
                Punct::HYPEN_SHY,
                Punct::HYPEN_WBR,
            ),
            'default' => Punct::HYPEN_NONE,
        ),
        'auto' => array(
            'desc'    => 'Автоматическая расстановка пунктуации',
            'type'    => 'bool',
            'default' => false,
        ),
    ),
);
