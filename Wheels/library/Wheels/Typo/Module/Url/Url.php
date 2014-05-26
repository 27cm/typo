<?php

namespace Wheels\Typo\Module\Url;

use Wheels\Typo\Typo;
use Wheels\Typo\Module\Module;
use Wheels\Utility;
use Wheels\Typo\Exception;

use Wheels\Utility\IDNA;

/**
 * Ссылки.
 *
 * Выделяет ссылки в тексте.
 *
 * @link http://wikipedia.org/wiki/URL
 * @link http://ru.wikipedia.org/wiki/%D0%9D%D0%BE%D1%80%D0%BC%D0%B0%D0%BB%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F_URL
 */
class Url extends Module
{
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
    // @link http://data.iana.org/TLD/tlds-alpha-by-domain.txt
    const HOST = '(?<host>(?:(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)|localhost|(?:[\wа-яё\-\~%]+\.)+(?:ac|academy|accountants|actor|ad|ae|aero|af|ag|agency|ai|airforce|al|am|an|ao|aq|ar|archi|arpa|as|asia|associates|at|au|audio|autos|aw|ax|axa|az|ba|bar|bargains|bayern|bb|bd|be|beer|berlin|best|bf|bg|bh|bi|bid|bike|biz|bj|black|blackfriday|blue|bm|bn|bo|boutique|br|bs|bt|build|builders|buzz|bv|bw|by|bz|ca|cab|camera|camp|capital|cards|care|career|careers|cash|cat|catering|cc|cd|center|ceo|cf|cg|ch|cheap|christmas|church|ci|citic|ck|cl|claims|cleaning|clinic|clothing|club|cm|cn|co|codes|coffee|college|cologne|com|community|company|computer|condos|construction|consulting|contractors|cooking|cool|coop|country|cr|credit|creditcard|cruises|cu|cv|cw|cx|cy|cz|dance|dating|de|democrat|dental|desi|diamonds|digital|directory|discount|dj|dk|dm|dnp|do|domains|dz|ec|edu|education|ee|eg|email|engineering|enterprises|equipment|er|es|estate|et|eu|eus|events|exchange|expert|exposed|fail|farm|feedback|fi|finance|financial|fish|fishing|fitness|fj|fk|flights|florist|fm|fo|foo|foundation|fr|frogans|fund|furniture|futbol|ga|gal|gallery|gb|gd|ge|gf|gg|gh|gi|gift|gl|glass|globo|gm|gmo|gn|gop|gov|gp|gq|gr|graphics|gratis|gripe|gs|gt|gu|guide|guitars|guru|gw|gy|haus|hiphop|hk|hm|hn|holdings|holiday|homes|horse|house|hr|ht|hu|id|ie|il|im|immobilien|in|industries|info|ink|institute|insure|int|international|investments|io|iq|ir|is|it|je|jetzt|jm|jo|jobs|jp|juegos|kaufen|ke|kg|kh|ki|kim|kitchen|kiwi|km|kn|koeln|kp|kr|kred|kw|ky|kz|la|land|lb|lc|lease|li|life|lighting|limited|limo|link|lk|loans|london|lr|ls|lt|lu|luxe|luxury|lv|ly|ma|maison|management|mango|marketing|mc|md|me|media|meet|menu|mg|mh|miami|mil|mk|ml|mm|mn|mo|mobi|moda|moe|monash|moscow|motorcycles|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|nagoya|name|nc|ne|net|neustar|nf|ng|ni|ninja|nl|no|np|nr|nu|nyc|nz|okinawa|om|onl|org|pa|paris|partners|parts|pe|pf|pg|ph|photo|photography|photos|pics|pictures|pink|pk|pl|plumbing|pm|pn|post|pr|pro|productions|properties|ps|pt|pub|pw|py|qa|qpon|quebec|re|recipes|red|reise|reisen|ren|rentals|repair|report|rest|reviews|rich|rio|ro|rocks|rodeo|rs|ru|ruhr|rw|ryukyu|sa|saarland|sb|sc|schule|sd|se|services|sexy|sg|sh|shiksha|shoes|si|singles|sj|sk|sl|sm|sn|so|social|sohu|solar|solutions|soy|sr|st|su|supplies|supply|support|surgery|sv|sx|sy|systems|sz|tattoo|tax|tc|td|technology|tel|tf|tg|th|tienda|tips|tj|tk|tl|tm|tn|to|today|tokyo|tools|town|toys|tp|tr|trade|training|travel|tt|tv|tw|tz|ua|ug|uk|university|uno|us|uy|uz|va|vacations|vc|ve|vegas|ventures|versicherung|vg|vi|viajes|villas|vision|vn|vodka|vote|voting|voto|voyage|vu|wang|watch|webcam|wed|wf|wien|wiki|works|ws|wtc|wtf|集团|在线|한국|ভারত|公益|公司|移动|我爱你|москва|қаз|онлайн|сайт|срб|орг|삼성|சிங்கப்பூர்|商标|商城|дети|中文网|中信|中国|中國|భారత్|ලංකා|ભારત|भारत|संगठन|网络|укр|香港|台湾|台灣|мон|الجزائر|عمان|ایران|امارات|بازار|الاردن|بھارت|المغرب|السعودية|مليسيا|شبكة|机构|组织机构|ไทย|سورية|рф|تونس|みんな|世界|ਭਾਰਤ|网址|游戏|مصر|قطر|இலங்கை|இந்தியா|新加坡|فلسطين|政务|xxx|xyz|yachts|ye|yokohama|yt|za|zm|zone|zw)))';

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
                Module::validateOption($name, $value);
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

        $this->getTypo()->getText()->preg_replace_callback($pattern, $callback);
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
    static public function condTarget(array $parts, Module $_this)
    {
        static $HTTP_HOST = null;
        if (!isset($HTTP_HOST))
            $HTTP_HOST = self::url_host_encode($_SERVER['HTTP_HOST'], $_this->getOption('idna'));

        return ($parts['host'] != $HTTP_HOST);
    }
}