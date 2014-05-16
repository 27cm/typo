<?php

return array(
    'options' => array(
        'charset' => array(
            'desc'    => 'Кодировка текста',
            'type'    => '\Wheels\Typo\Type\Tcharset',
            'default' => 'UTF-8',
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
        'modules' => array(
            'desc'    => 'Используемые модули',
            'type'    => '\Wheels\Typo\Type\Tmodule[]',
            'default' => array('core', 'html', 'nobr', 'punct', 'space', 'symbol', 'url'),
        ),
    ),
);
