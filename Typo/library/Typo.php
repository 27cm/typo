<?php

use Typo\Loader;
use Typo\Module;
use Typo\Text;
use Typo\Utility;
use Typo\Exception;

/**
 * Разделитель пути. Для Windows - "\", для Linux и остальных — "/".
 */
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * ??? Для Windows - ";", для Linux и остальных — ":".
 */
if (!defined('PS')) define('PS', PATH_SEPARATOR);

/**
 * Каталог с библиотекой Typo.
 */
define('TYPO_DIR', realpath(dirname(__FILE__)));

require_once TYPO_DIR . DS . 'Typo' . DS . 'functions.php';
require_once TYPO_DIR . DS . 'Typo' . DS . 'Loader.php';
$loader = new Loader('Typo', TYPO_DIR);
$loader->register();

/**
 * Типограф.
 *
 * @copyright Copyright (c) Захаров А. Е., 2012 - 2014
 *
 * @version 0.2 2014-02-02
 */
class Typo extends Module
{
    /**
     * Используемые коды символов.
     *
     * @var string[]
     */
    public $chr = array();

    /**
     * Спецсимволы
     *
     * @link http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references
     *
     * @var array
     */
    static public $chars = array(
        'nbsp'   => array('name' => '&nbsp;',   'code' => 160),    // Неразрывный пробел
        'thinsp' => array('name' => '&thinsp;', 'code' => 8201),   // Полупробел
        'sect'   => array('name' => '&sect;',   'code' => 167),    // Знак параграфа
        'copy'   => array('name' => '&copy;',   'code' => 169),    // Знак охраны авторского права
        'reg'    => array('name' => '&reg;',    'code' => 174),    // Знак правовой охраны товарного знака
        'trade'  => array('name' => '&trade;',  'code' => 8482),   // Товарный знак
        'deg'    => array('name' => '&deg;',    'code' => 176),    // Знак градуса
        'sup1'   => array('name' => '&sup1;',   'code' => 185),    // Верхний индекс "1"
        'sup2'   => array('name' => '&sup2;',   'code' => 178),    // Верхний индекс "2"
        'sup3'   => array('name' => '&sup3;',   'code' => 179),    // Верхний индекс "3"
        'frac14' => array('name' => '&frac14;', 'code' => 188),    // Простая дробь "одна четвёртая"
        'frac12' => array('name' => '&frac12;', 'code' => 189),    // Простая дробь "одна вторая"
        'frac13' => array('name' => '1/3',      'code' => 8531),   // Простая дробь "одна треть"
        'frac34' => array('name' => '&frac34;', 'code' => 190),    // Простая дробь "три четверти"
        'times'  => array('name' => '&times;',  'code' => 215),    // Знак умножения
        'bull'   => array('name' => '&bull;',   'code' => 8226),   // Маркер списка (буллит)
        'hellip' => array('name' => '&hellip;', 'code' => 8230),   // Горизонтальное многоточие
        'le'     => array('name' => '&le;',     'code' => 8804),   // Меньше или равно
        'ge'     => array('name' => '&ge;',     'code' => 8805),   // Больше или равно
        'cong'   => array('name' => '&cong;',   'code' => 8773),   // Приблизительно равно
        'plusmn' => array('name' => '&plusmn;', 'code' => 177),    // Плюс-минус
        'ndash'  => array('name' => '&ndash;',  'code' => 8211),   // Тире длины N
        'mdash'  => array('name' => '&mdash;',  'code' => 8212),   // Тире длины M
        'ne'     => array('name' => '&ne;',     'code' => 8800),   // Не равно
        'minus'  => array('name' => '&minus;',  'code' => 8722),   // Знак минус
        'laquo'  => array('name' => '&laquo;',  'code' => 171),    // Направленная влево двойная угловая кавычка
        'raquo'  => array('name' => '&raquo;',  'code' => 187),    // Направленная вправо двойная угловая кавычка
        'ldquo'  => array('name' => '&ldquo;',  'code' => 8220),   // Двойная левая кавычка
        'rdquo'  => array('name' => '&rdquo;',  'code' => 8221),   // Двойная правая кавычка
        'permil' => array('name' => '&permil;', 'code' => 8240),   // Промилле
        'larr'   => array('name' => '&larr;',   'code' => 8592),   // Стрелка влево
        'uarr'   => array('name' => '&uarr;',   'code' => 8593),   // Стрелка вверх
        'rarr'   => array('name' => '&rarr;',   'code' => 8594),   // Стрелка вправо
        'darr'   => array('name' => '&darr;',   'code' => 8595),   // Стрелка вниз
        'harr'   => array('name' => '&harr;',   'code' => 8596),   // Стрелка влево-вправо
        'lArr'   => array('name' => '&lArr;',   'code' => 8656),   // Двойная стрелка влево
        'uArr'   => array('name' => '&uArr;',   'code' => 8657),   // Двойная стрелка вверх
        'rArr'   => array('name' => '&rArr;',   'code' => 8658),   // Двойная стрелка вправо
        'dArr'   => array('name' => '&dArr;',   'code' => 8659),   // Двойная стрелка вниз
        'hArr'   => array('name' => '&hArr;',   'code' => 8660),   // Двойная стрелка влево-вправо
    );

    /**
     * Настройки типографа по умолчанию
     *
     * @var array
     */
    protected $default_options = array(
        /**
         * Кодировка текста.
         *
         * @var 'AUTO'|string
         */
        'charset' => 'UTF-8',

        /**
         * Режим кодирования спецсимволов.
         *
         * @var 'AUTO'|'MODE_NONE'|'MODE_NAMES'|'MODE_CODES'|'MODE_HEX_CODES'
         */
        'encoding' => self::AUTO,

        /**
         * Используемые модули.
         *
         * @var string[]
         */
        'modules' => array('html', 'punct', 'space', 'url', 'quote', 'math', 'filepath', 'smile'),

        /**
         * Включение HTML в тексте на входе.
         *
         * Пример:
         * html-in-enabled = 0      "&lt;div&gt;текст site.com&lt;/div&gt;"
         * html-in-enabled = 1      "<div>текст site.com</div>"
         *
         * @var bool
         */
        'html-in-enabled' => true,

        /**
         * Включение HTML в тексте на выходе.
         *
         * Пример:
         * html-out-enabled = 0     "текст site.com"
         * html-out-enabled = 1     "текст <a href="http://site.com">site.com</a>"
         *
         * @var bool
         */
        'html-out-enabled' => true,

        /**
         * Тип HTML документа.
         *
         * @var 'DOCTYPE_HTML4_STRICT'|'DOCTYPE_HTML4_TRANSITIONAL'|'DOCTYPE_HTML4_FRAMESET'|'DOCTYPE_XHTML1_STRICT'|
         *      'DOCTYPE_XHTML1_TRANSITIONAL'|'DOCTYPE_XHTML1_FRAMESET'|'DOCTYPE_XHTML11'|'DOCTYPE_XHTML5'
         */
        'html-doctype' => self::DOCTYPE_HTML5,

        /**
         * Спецсимволы.
         *
         * @var bool
         */
        'spec' => true,

        /**
         * Неразрывные пробелы.
         *
         * @var bool
         */
        'nbsp' => true,

        /**
         * Вставлять <br> перед каждым переводом строки.
         *
         * @var bool
         */
        'nl2br' => true,

        /**
         * Использование <nobr>.
         *
         * @var bool
         */
        'nobr' => true,

        /**
         * Замена всех букв 'ё' на 'е'.
         *
         * @var bool
         */
        'e-convert' => false,
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 10,
        'B' => 10,
        'C' => 25,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );

    /**
     * Версия типографа
     *
     * @var string
     */
    static private $version = '0.1';


    // --- Типы документов HTML ---

    /** HTML4 Strict */
    const DOCTYPE_HTML4_STRICT        = 'DOCTYPE_HTML4_STRICT';

    /** HTML4 Transitional */
    const DOCTYPE_HTML4_TRANSITIONAL  = 'DOCTYPE_HTML4_TRANSITIONAL';

    /** HTML4 Frameset */
    const DOCTYPE_HTML4_FRAMESET      = 'DOCTYPE_HTML4_FRAMESET';

    /** XHTML1 Strict */
    const DOCTYPE_XHTML1_STRICT       = 'DOCTYPE_XHTML1_STRICT';

    /** XHTML1 Transitional */
    const DOCTYPE_XHTML1_TRANSITIONAL = 'DOCTYPE_XHTML1_TRANSITIONAL';

    /** XHTML1 Frameset */
    const DOCTYPE_XHTML1_FRAMESET     = 'DOCTYPE_XHTML1_FRAMESET';

    /** XHTML11 */
    const DOCTYPE_XHTML11             = 'DOCTYPE_XHTML11';

    /** HTML5 */
    const DOCTYPE_HTML5               = 'DOCTYPE_HTML5';


    // --- Режимы кодирования спецсимволов ---


    /** Не кодировать. */
    const MODE_NONE  = 'MODE_NONE';

    /** В виде имён. */
    const MODE_NAMES = 'MODE_NAMES';

    /** В виде кодов. */
    const MODE_CODES = 'MODE_CODES';

    /** В виде шестнадцатеричных кодов. */
    const MODE_HEX_CODES   = 'MODE_HEX_CODES';


    // --- Заменители ---

    /** Элемент. */
    const REPLACER = 'E';

    /** Видимый элемент. */
    const VISIBLE = '[[[%s%u]]]';

    /** Невидимый элемент. */
    const INVISIBLE = '{{{%s%u}}}';


    // --- Прочие константы ---

    /** Автоматическое определение значения настройки. */
    const AUTO = 'AUTO';


    // --- Открытые методы класса ---

    /**
     * Проверка значения параметра (с возможной корректировкой).
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     *
     * @return void
     */
    public function validateOption($name, &$value)
    {
        switch($name)
        {
            // Кодировка текста
            case 'charset' :
                if($value != self::AUTO)
                {
                    $value = strtolower($value);
                    $result = iconv($value, 'UTF-8', '');
                    if($result === false)
                        return self::throwException(Exception::E_OPTION_VALUE, "Неизвестная кодировка '$value'");
                }
            break;

            // Режим кодирования спецсимволов
            case 'encoding' :
                if($value != self::AUTO)
                {
                    if(!Utility::validateConst(get_called_class(), $value, 'MODE'))
                        return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный режим кодирования спецсимволов '$value'");
                }
            break;

            // Тип документа HTML
            case 'html-doctype' :
                if(!Utility::validateConst(get_called_class(), $value, 'DOCTYPE'))
                    return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный тип документа '$value'");
            break;

            default : Module::validateOption($name, $value);
        }
    }

    /**
     * Типографирует текст.
     *
     * <code>
     * $typo = new Typo;
     * echo $typo->execute('Какой-то текст...');
     * </code>
     *
     * @param string $text  Исходный текст.
     *
     * @return string Оттипографированный текст.
     */
    public function execute($text)
    {
        $this->text = ($text instanceof Text) ? $text : new Text($text);

        // @todo: исправить ошибки повторного вызова
        // $this->saveOptions() и $this->restoreOptions()
        if($this->options['charset'] == self::AUTO)
            $this->setOption('charset', $this->text->detectCharset());

        if($this->options['encoding'] == self::AUTO)
        {
            switch($this->options['charset'])
            {
                case 'utf-8' :
                    $this->setOption('encoding', self::MODE_NONE);
                break;
                default :
                    $this->setOption('encoding', self::MODE_NAMES);
                break;
            }
        }

        $charset = $this->options['charset'];
        $int_encoding = mb_internal_encoding();
        $default_charset = 'UTF-8';


        // Меняем кодировку текста
        mb_internal_encoding($default_charset);
        if($charset != $default_charset)
            $this->text->iconv($charset, $default_charset);

        // Выполнение всех стадий
        foreach(self::getStages() as $stage)
            $this->setStage($stage)->executeStage();

        // Восстанавливаем кодировку текста
        if($charset != $default_charset)
            $this->text->iconv($default_charset, $charset);
        mb_internal_encoding($int_encoding);

        return $this->text;
    }


    // --- Защищённые методы класса ---

    /**
     * Стадия A.
     *
     * @return void
     */
    protected function stageA()
    {

    }

    /**
     * Стадия B.
     */
    protected function stageB()
    {
        $chars = array(
            '"' => array(
                '&#34;', '&#132;', '&#147;', '&#171;', '&#187;', '&#8220;', '&#8221;', '&#8222;',
                '&quot;', '&laquo;', '&raquo;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&bdquo;',
                '"', '"', '„', '“', '”',
            ),
            ' ' => array(
                '&nbsp;', '&ensp;', '&emsp;', '&thinsp;',
                '&#160;', '&#8194;', '&#8195;', '&#8194;',
            ),
            '-' => array(
                '&#8211;', '&#8212;', '&#8209;', '&#151;',
                '&ndash;', '&mdash;', '&minus;',
                '–', '−', '—', '—', '—',
            ),
        );

        foreach($chars as $replace => $group)
             $this->text->replace($group, $replace);
    }

    /**
     * Стадия C.
     *
     * @return void
     */
    protected function stageC()
    {
        $this->text->popStorage(self::REPLACER, self::INVISIBLE);
        $this->text->popStorage(self::REPLACER, self::VISIBLE);

        // Вставлять <br> перед каждым переводом строки
        if($this->options['nl2br'])
           $this->text->nl2br();
    }

    protected function onChangeOption($name, $value)
    {
        switch($name)
        {
            // Режим кодирования спецсимволов
            case 'encoding' :
                if($value != self::AUTO)
                {
                    switch($value)
                    {
                        case self::MODE_NONE :
                            foreach(self::$chars as $key => $c)
                                $this->chr[$key] = Utility::chr($c['code']);
                        break;
                        case self::MODE_CODES :
                            foreach(self::$chars as $key => $c)
                                $this->chr[$key] = sprintf('&#%u;', $c['code']);
                        break;
                        case self::MODE_HEX_CODES :
                            foreach(self::$chars as $key => $c)
                                $this->chr[$key] = sprintf('&#x%x;', $c['code']);
                        break;
                        case self::MODE_NAMES :
                            foreach(self::$chars as $key => $c)
                                $this->chr[$key] = $c['name'];
                        break;
                    }
                }
            break;
        }
    }


    // --- Статические методы класса ---

    /**
     * Возвращает номер версии типографа.
     *
     * @return string
     */
    static public function getVersion()
    {
        return self::$version;
    }

    /**
     * Установливает значения параметров настроек по умолчанию,
     * затем установливает заданные значения параметров настроек
     * и типографирует заданный текст.
     *
     * @param string $text      Исходный текст.
     * @param array  $options   Ассоциативный массив ('название параметра' => 'значение').
     *
     * @uses \Typo::execute()
     *
     * @return string Оттипографированный текст.
     */
    static public function staticExecute($text, array $options = array())
    {
        $typo = new self($options);

        return $typo->execute($text);
    }
}