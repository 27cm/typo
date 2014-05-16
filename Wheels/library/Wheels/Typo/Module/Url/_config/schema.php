<?php

return array(
    'options' => array(
        'attrs'       => array(
            'desc'    => 'Дополнительные атрибуты',
            'type'    => 'array',
            'default' => array(
                'target' => array(
                    'name'  => 'target',
                    'value' => '_blank',
                    'cond'  => '\Wheels\Typo\Module\Url::condTarget',
                ),
            ),
        ),
        'idn-convert' => array(
            'desc'    => 'Преобразование интернационализованных доменных имён (IDN)',
            'type'    => 'bool',
            'default' => true,
        ),
    ),
);
