<?php

return array(
    'options' => array(
        'attrs' => array(
            'desc'    => 'Атрибуты',
            'type'    => 'Wheels\Typo\Type\Tattrs',
            'default' => array(
                'target' => array(
                    'value' => '_blank',
                    'cond'  => 'Wheels\Typo\Module\Url\Url::condTarget',
                ),
            ),
        ),
        'normalize' => array(
            'desc'    => 'Нормализация (канонизация) URL',
            'type'    => 'bool',
            'default' => true,
        ),
        'idna' => array(
            'desc'    => 'Преобразование интернационализованных доменных имён (IDN)',
            'type'    => 'bool',
            'default' => true,
        ),
    ),
);
