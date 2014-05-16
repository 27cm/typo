<?php

namespace Wheels\Typo\Module\Url;

use Wheels\Typo\Typo;
use Wheels\Utility;
use Wheels\Typo\Module\Url;

/**
 * Ссылки на аккаунты в Twitter и хеш-теги.
 */
class Twitter extends Url
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    static protected $_default_options
        = array(
            /**
             * Дополнительные атрибуты.
             *
             * @var array
             */
            'attrs'   => array(
                array(
                    'name'  => 'target',
                    'value' => '_blank',
                ),
            ),

            /**
             * Используемые модули.
             *
             * @var string[]
             */
            'modules' => array(),
        );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static protected $_order
        = array(
            'A' => 25,
            'B' => 0,
            'C' => 0,
            'D' => 15,
            'E' => 0,
            'F' => 0,
        );

    static public $url = 'http://twitter.com';


    // --- Заменитель ---

    const REPLACER = 'TW';


    // --- Открытые методы ---

    /**
     * Стадия A.
     *
     * Заменяет все twitter-логины и хештеги на заменитель.
     *
     * @return void
     */
    public function stageA()
    {
        if ($this->_typo->getOption('html-out-enabled')) {
            $_this = $this;
            $rules = array(
                // Ретвиты (RT @login:)
                '~\bRT\h@(\w+):~i'    => function ($m) use ($_this) {
                        $login = $m[1];

                        $href = Twitter::$url . "/{$login}";
                        $attrs = array(
                            'href'  => $href,
                            'title' => "Перейти в твиттер $login"
                        );
                        $parts = array('login' => $login);
                        $_this->setAttrs($parts, $attrs);

                        $data = 'Ретвит' . Utility::createElement('a', "@{$login}", $attrs);

                        return $_this->getTypo()->getText()->pushStorage($data, Twitter::REPLACER, Typo::VISIBLE);
                    },

                // Логины (@login)
                '~(?<!{a})@(\w+)~i'   => function ($m) use ($_this) {
                        $login = $m[1];

                        $href = Twitter::$url . "/{$login}";
                        $attrs = array(
                            'href'  => $href,
                            'title' => "Перейти в твиттер $login"
                        );
                        $parts = array('login' => $login);
                        $_this->setAttrs($parts, $attrs);

                        $data = Utility::createElement('a', $m[0], $attrs);

                        return $_this->getTypo()->getText()->pushStorage($data, Twitter::REPLACER, Typo::VISIBLE);
                    },

                // Хештеги
                '~(?<!{a})#({a}+)~iu' => function ($m) use ($_this) {
                        $hash = $m[1];

                        $href = Twitter::$url . "/search?q=%23{$hash}&amp;src=hash";
                        $attrs = array(
                            'href' => $href,
                        );
                        $parts = array('hash' => $hash);
                        $_this->setAttrs($parts, $attrs);

                        $data = Utility::createElement('a', $m[0], $attrs);

                        return $_this->getTypo()->getText()->pushStorage($data, Twitter::REPLACER, Typo::VISIBLE);
                    },
            );

            $this->applyRules($rules);
        }
    }
}