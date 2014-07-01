<?php

return array(
    'options' => array(
        'width' => array(
            'desc'    => 'Ширина изображений смайликов',
            'type'    => 'int',
            'default' => 20,
        ),
        'height' => array(
            'desc'    => 'Высота изображений смайликов',
            'type'    => 'int',
            'default' => 20,
        ),
        'tag' => array(
            'desc'    => 'HTML тег для изображений',
            'type'    => 'string',
            'default' => 'img',
        ),
        'attrs' => array(
            'desc'    => 'Атрибуты',
            'type'    => 'Wheels\Typo\Type\Tattrs',
            'default' => array(
                'src' => array(
                    'value' => '/img/emoticons/skype/{id}.gif',
                ),
                'width' => array(
                    'value' => '{width}',
                ),
                'height' => array(
                    'value' => '{height}',
                ),
                'title' => array(
                    'value' => '{emoticon}',
                ),
                'alt' => array(
                    'value' => '{emoticon}',
                ),
            ),
        ),
        /* @todo: Добавить параметр, ограничивающий выбор смайликов */
    ),
);
