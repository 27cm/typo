<?php

namespace Wheels\Typo\Module\Url;

use Wheels\Typo\Typo;
use Wheels\Typo\Module\AbstractModule;
use Wheels\Utility;
use Wheels\Typo\Exception;

use Wheels\Utility\IDNA;

/**
 * Ссылки.
 *
 * Выделяет ссылки в тексте.
 *
 * @link http://wikipedia.org/wiki/URL
 */
class Url extends AbstractModule
{
    /**
     * @see \Wheels\Typo\_configSchema::$_config_schema
     */
    static protected $_configSchema
        = array(

        );

    /**
     * @see \Wheels\Typo\Module::$_order
     */
    static protected $_order
        = array(
            'A' => 20,
            'B' => 0,
            'C' => 0,
            'D' => 20,
            'E' => 0,
            'F' => 0,
        );


    // --- Регулярные выражения ---

    /** Схема (схема обращения к ресурсу, сетевой протокол) */
    const SCHEME = '(?<scheme>cap|ftp|https?|nfs)';

    /** Логин (имя пользователя, используемое для доступа к ресурсу) */
    const USER = '(?<user>[\w\-\.\~%!\$&\'()*+,;=]+)';

    /** Пароль (пароль указанного пользователя) */
    const PASSWORD = '(?<password>[\w\-\.\~%!\$&\'()*+,;=]+)';

    /** www (www-префикс) */
    const WWW = '(?<www>www)';

    /** IP-адрес */
    const IP = '(?<ip>(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?))';

    /** Хост (полностью прописанное доменное имя хоста в системе DNS или IP-адрес хоста) */
    const HOST = '(?<host>(?:(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)|localhost|(?:[\wа-яё\-\~%]+\.)+(?:рф|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|[a-z]{2})))';

    /** Путь (уточняющая информация о месте нахождения ресурса; зависит от протокола) */
    const PATH = '(?<path>(?:/[\wа-яё\-\.\~%!\$&\'()*+,;=:@]*[\wа-яё\~%\$&])*/?)';

    /** Порт (порт хоста для подключения) */
    const PORT = '(?<port>\d{1,5})';

    /** Параметры (строка запроса с передаваемыми на сервер параметрами) */
    const QUERY = '(?<query>([\wа-яё\-\.+,;:%]*[\wа-яё\-%&\~](\[[\wа-яё\-\.+,;:%]*\])*(?:=(?:("[^"]*")|(\'[^\']*\')|([\wа-яё\~=\-%!\$\'()*+,;:@/?]*[\wа-яё\-%]))?)?)+)';

    /** Якорь (идентификатор «якоря», ссылающегося на некоторую часть открываемого документа) */
    const HASH = '(?<hash>[\wа-яё\-\.\~%!$&\'()*+,;=:@/?]*)';


    // --- Заменитель ---

    const REPLACER = 'URL';


    // --- Открытые методы ---

    /**
     * @see \Wheels\Typo\Module::validateOption()
     */
    public function validateOption($name, &$value)
    {
        switch ($name) {
            case 'attrs' :
                if (!is_array($value)) {
                    return self::throwException(
                        Exception::E_OPTION_VALUE,
                        "Значение параметра '$name' должно быть массивом, а не " . gettype($value)
                    );
                }

                foreach ($value as &$attr) {
                    if (!is_array($attr) || !array_key_exists('name', $attr) || !array_key_exists('value', $attr))
                        return self::throwException(
                            Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом элементов array('name' => '...', 'value' => '...', ['cond' => '...'])"
                        );
                    if (!array_key_exists('cond', $attr))
                        $attr['cond'] = true;
                }
                break;

            default :
                AbstractModule::validateOption($name, $value);
        }
    }

    /**
     *
     * @param type  $parts
     * @param array $attrs
     */
    public function setAttrs($parts, array &$attrs)
    {
        foreach ($this->getOption('attrs') as $attr) {
            $a_cond = $attr['cond'];
            if (is_callable($a_cond))
                $a_cond = call_user_func($a_cond, $parts, $this);

            if ($a_cond) {
                $a_name = $attr['name'];
                if (is_callable($a_name))
                    $a_name = call_user_func($a_name, $parts, $this);

                $a_value = $attr['value'];
                if (is_callable($a_value))
                    $a_value = call_user_func($a_value, $parts, $this);

                $attrs[$a_name] = $a_value;
            }
        }
    }


    // --- Защищенные методы ---

    /**
     * Стадия A.
     *
     * Заменяет все ссылки на заменитель.
     */
    public function stageA()
    {
        $_this = $this;

        $callback = function ($parts) use ($_this) {
            $match = $parts[0];

            $parts += array('scheme' => '', 'slashes' => '', 'mailto' => '', 'user' => '', 'password' => '',
                            'www'    => '', 'host' => '', 'port' => '', 'path' => '', 'query' => '', 'hash' => '');

            // Создаём перменные
            $scheme = $slashes = $mailto = $user = $password = $www = $host = $port = $path = $query = $hash = null;
            foreach ($parts as $key => $value) {
                if (is_int($key))
                    unset($parts[$key]);
                else {
                    if (mb_strlen($value) == 0)
                        $parts[$key] = null;
                    ${$key} =& $parts[$key];
                }
            }

            // Формируем будущую ссылку <a href="{$href}">{$value}</a>
            $value = $href = '';

            $localhost = ($host == 'localhost' || preg_match('~' . Url::IP . '~', $host));

            // Схема
            if (isset($scheme)) {
                $scheme = mb_strtolower($scheme);
                $href = $scheme . '://';

                // Протоколы http, https и mailto опускаем
                if (!in_array($scheme, array('http', 'https')))
                    $value = $href;
            } elseif (isset($slashes)) {
                $href = $slashes;
            } elseif (isset($mailto) || (isset($user) && !isset($password) && !$localhost)) {
                $href = 'mailto:';
            } else {
                $href = 'http://';
            }

            // Логин и пароль
            if (isset($user)) {
                $href .= rawurlencode($user);
                $value .= $user;

                if (isset($password)) {
                    $href .= ':' . rawurlencode($password);
                    $value .= ':' . $password;
                }

                $href .= '@';
                $value .= '@';
            }

            // www
            if (isset($www))
                $href .= 'www.';

            // Хост
            $host = mb_strtolower($host);

            // Подсвечиваем localhost только, если задан протокол
            if ($localhost && !(isset($scheme) || isset($slashes)))
                return $match;

            $href .= Url::url_host_encode($host, $_this->getOption('idna'));
            $value .= $host;

            // Порт
            if (isset($port)) {
                $href .= ':' . $port;
                $value .= ':' . $port;
            }

            // URL-путь
            if (isset($path)) {
                $href .= Url::url_path_encode($path);
                $value .= $path;
            }

            // Параметры
            if (isset($query)) {
                $href .= '?' . Url::url_query_encode($query);
                $value .= '?' . $query;
            }

            // Якорь
            if (isset($hash)) {
                $href .= '#' . urlencode($hash);
                $value .= '#' . $hash;
            }

            $value = htmlentities($value, ENT_QUOTES, 'utf-8');

            if ($_this->_typo->getOption('html-out-enabled')) {
                $attrs = array('href' => $href);
                $_this->setAttrs($parts, $attrs);

                $data = Utility::createElement('a', $value, $attrs);
            } else
                $data = $value;

            return $_this->getTypo()->getText()->pushStorage($data, Url::REPLACER, Typo::VISIBLE);
        };

        // [[<схема>:]//|mailto:][<логин>[:<пароль>]@][<www>.]<хост>[:<порт>][<URL‐путь>][?<параметры>][#<якорь>]
        $pattern = '((' . self::SCHEME . ':)?(?<slashes>//)|(?<mailto>mailto)\:)?'
            . '(' . self::USER . '(\:' . self::PASSWORD . ')?@)?'
            . '(' . self::WWW . '\.)?' . self::HOST . '(\:' . self::PORT . ')?' . self::PATH
            . '(\?' . self::QUERY . ')?' . '(#' . self::HASH . ')?';
        $pattern = $this->preg_wrap($pattern);

        $this->_typo->text->preg_replace_callback($pattern, $callback);
    }

    /**
     * Стадия D.
     *
     * Восстанавливает ссылки.
     */
    public function stageD()
    {
        $class = get_called_class();

        $this->getTypo()->getText()->popStorage($class::REPLACER, Typo::VISIBLE);
    }

    /**
     * Создаёт регулярное выражение для выделения ссылок.
     *
     * @param string $pattern Базовое регулярное выражение.
     *
     * @return string
     */
    protected function preg_wrap($pattern)
    {
        $pattern = '~(?<=[\wа-яё]|\s|\b|^)' . $pattern . '(?![\wа-яё])~iu';

        return $pattern;
    }


    // --- Статические методы ---

    /**
     * URL-кодирование хоста.
     *
     * @link http://ru.wikipedia.org/wiki/Punycode
     * @link http://phlymail.com/en/downloads/idna-convert.html
     *
     * @param string $host
     * @param bool   $idna
     *
     * @return string
     */
    static public function url_host_encode($host, $idna = true, $_this = null)
    {
        // @todo: исключение (warning), если библиотека не подключена
        // @todo: нужно ли парсить отдельные части хоста?
        if ($idna && preg_match('~[^\x00-\xA7]~u', $host)) {
            $idna = IDNA::singleton();
            $host = $idna->encode($host);
        }

        return $host;
    }

    /**
     * URL-кодирование пути.
     *
     * @param string $path
     *
     * @return string
     */
    static public function url_path_encode($path)
    {
        $parts = preg_split('~([/;=])~', $path, -1, PREG_SPLIT_DELIM_CAPTURE);

        $encoded_path = '';
        foreach ($parts as $part) {
            if (preg_match('~[/;=]~', $part))
                $encoded_path .= $part;
            else
                $encoded_path .= rawurlencode($part);
        }

        $encoded_path = str_replace('/%7E', '/~', $encoded_path);

        return $encoded_path;
    }


    /**
     * URL-кодирование запроса.
     *
     * @param string $query
     *
     * @return string
     */
    static public function url_query_encode($query)
    {
        $parts = preg_split('~([&=+])~', $query, -1, PREG_SPLIT_DELIM_CAPTURE);

        $encoded_query = '';
        foreach ($parts as $part) {
            if (preg_match('~[&=+]~', $part))
                $encoded_query .= $part;
            else
                $encoded_query .= urlencode($part);
        }

        return $encoded_query;
    }

    /**
     * URL-кодирование ссылки.
     *
     * @param string $url
     *
     * @return string
     */
    static public function urlencode($url)
    {
        $matches = parse_url($url);
        if (!$matches)
            return $url;

        $parts = array('scheme' => '', 'user' => '', 'pass' => '', 'host' => '', 'port' => '', 'path' => '',
                       'query'  => '', 'fragment' => '');

        foreach (array_keys($parts) as $i) {
            if (isset($matches[$i]))
                $parts[$i] = $matches[$i];
        }

        list($scheme, $user, $pass, $host, $port, $path, $query, $hash) = array_values($parts);

        if (!empty($scheme))
            $scheme .= '://';

        if (!empty($user) && !empty($pass)) {
            $user = rawurlencode($user) . ':';
            $pass = rawurlencode($pass) . '@';
        } elseif (!empty($user))
            $user .= '@';

        if (!empty($port) && !empty($host))
            $host = '' . $host . ':';
        elseif (!empty($host))
            $host = $host;

        if (!empty($path))
            $path = self::url_path_encode($path);

        if (!empty($query))
            $query = '?' . self::url_query_encode($query);

        if (!empty($hash))
            $hash = '#' . urlencode($hash);

        return implode('', array($scheme, $user, $pass, $host, $port, $path, $query, $hash));
    }

    /**
     * Проверяет, является ли ссылка внутренней.
     *
     * @param array               $parts Массив частей URL.
     * @param \Wheels\Typo\Module $_this Вызывающий модуль.
     *
     * @return bool
     */
    static public function condTarget(array $parts, AbstractModule $_this)
    {
        static $HTTP_HOST = null;
        if (!isset($HTTP_HOST))
            $HTTP_HOST = self::url_host_encode($_SERVER['HTTP_HOST'], $_this->getOption('idna'));

        return ($parts['host'] != $HTTP_HOST);
    }
}