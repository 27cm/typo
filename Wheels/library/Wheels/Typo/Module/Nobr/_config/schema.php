<?php

return array(
    'options' => array(
        'open' => array(
            'desc'    => 'Открывающий тег для неразрывных конструкций',
            'type'    => 'string',
            'default' => '<span style="word-spacing:nowrap;">',
//                        <span class="nowrap">
//                        <nobr> (тег отсутствует в стандартах HTML)
        ),
        'close' => array(
            'desc'    => 'Закрывающий тег для неразрывных конструкций',
            'type'    => 'string',
            'default' => '</span>',
//                        </nobr>
        ),
    ),
);
