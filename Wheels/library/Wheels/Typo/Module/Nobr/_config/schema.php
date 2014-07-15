<?php

return array(
    'options' => array(
        'tag' => array(
            'desc'    => 'Тег для неразрывных конструкций',
            'type'    => 'array',
            'default' => array('name' => 'span', 'attrs' => array('style' => 'word-spacing:nowrap')),
//                       array('name' => 'nobr', 'attrs' => array()),  // тег отсутствует в стандартах HTML
        ),
    ),
);
