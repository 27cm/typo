<?php

return array(
    'options' => array(
        'safe-blocks' => array(
            'desc'    => 'Безопасные блоки, содержимое которых не обрабатывается типографом',
            'type'    => 'string[]',
            'default' => array('<!-- -->', 'code', 'comment', 'pre', 'script', 'style'),
        ),
        'typo-attrs' => array(
            'desc'    => 'Атрибуты тегов, подлежащие типографированию',
            'type'    => 'string[]',
            'default' => array('title', 'alt'),
        ),
        'paragraphs' => array(
            'desc'    => 'Простановка параграфов',
            'type'    => 'bool',
            'default' => true,
        ),
    ),
);
