<?php

namespace Typo\Module\Url;

use Typo;
use Typo\Utility;
use Typo\Module\Url;

/**
 * SSH-ссылки.
 *
 * Выделяет ssh-ссылки в тексте.
 *
 * @link http://wikipedia.org/wiki/Secure_Shell
 * @link http://tools.ietf.org/html/draft-ietf-secsh-scp-sftp-ssh-uri
 */
class Ssh extends Url
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array(
        /**
         * Дополнительные атрибуты.
         *
         * @var array
         */
        'attrs' => array(),

        /**
         * IDNA.
         *
         * @var bool
         */
        'idna' => true,

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
    static public $order = array(
        'A' => 20,
        'B' => 0,
        'C' => 15,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Регулярные выражения ---

    /** Схема (схема обращения к ресурсу, сетевой протокол) */
    const SCHEME = '(?<scheme>ssh)';

    /** Отпечаток */
    const FINGERPRINT = '(?<fingerprint>(?:(?:[\da-f]{2}-)*|(?:[\da-f]{2}\:)*)[\da-f]{2})';


    // --- Заменитель ---

    const REPLACER = 'SSH';


    // --- Открытые методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет все ssh-ссылки на заменитель.
     *
     * @return void
     */
    protected function stageA()
    {
        $_this = $this;

        $callback = function($matches) use($_this)
        {
            $href = Url::urlencode($matches[0]);
            $value = htmlentities($matches[0], ENT_QUOTES, 'utf-8');

            // @todo: исправить массив $parts
            $parts = array('url' => $href);

            if($_this->typo->getOption('html-out-enabled'))
            {
                $attrs = array('href' => $href);
                $_this->setAttrs($parts, $attrs);

                $data = Utility::createElement('a', $value, $attrs);
            }
            else
                $data = $value;

            return $_this->text->pushStorage($data, Ssh::REPLACER, Typo::VISIBLE);
        };

        // <схема>://[<логин>[;fingerprint=<отпечаток>]@]<хост>[:<порт>][<URL-путь>]
        $pattern = self::SCHEME . '://'
                 . '(' . Url::USER . '(\:' . self::PASSWORD . ')?(;fingerprint=ssh-(dss|rsa)-' .  self::FINGERPRINT. ')?@)?'
                 . self::HOST . '(:' . self::PORT . ')?' . self::PATH;
        $pattern = $this->preg_wrap($pattern);

        $this->typo->text->preg_replace_callback($pattern, $callback);
    }
}