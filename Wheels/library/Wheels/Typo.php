<?php

namespace Wheels;

use Wheels\Typo\Module;
use Wheels\Typo\Text;
use Wheels\Typo\Utility;
use Wheels\Typo\Exception;

if(version_compare(PHP_VERSION, '5.3.0', '<'))
    trigger_error('Для работы типограф требуется версия php 5.3.0 или выше', E_USER_ERROR);

if(!extension_loaded('mbstring'))
{
	$ext = ((substr(PHP_OS, 0, 3) == 'WIN') ? 'dll' : 'so');
	if(!ini_get('enable_dl') || !dl('mbstring' . $ext))
        trigger_error('Для работы типографа требуется расширение mbstring', E_USER_ERROR);
}

/**
 * Каталог с библиотекой Wheels\Typo.
 */
define('TYPO_DIR', dirname(__FILE__));

/**
 * Директория с файлами конфигурации.
 */
define('TYPO_CONFIG_DIR', realpath(TYPO_DIR . DS . '..' . DS . '..' . DS . 'config'));
if(!TYPO_CONFIG_DIR)
    trigger_error('Директория с файлами конфигурации типографа не найдена', E_USER_ERROR);

require_once TYPO_DIR . DS . 'Typo' . DS . 'functions.php';

/**
 * Типограф.
 *
 * @version 0.3 2014-02-16
 */
class Typo extends Module
{
    /**
     * Используемые символы.
     *
     * @var string[]
     */
    public $chr = array();

    /**
     * Коды символов.
     *
     * @link http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references
     *
     * @var array
     */
    static public $chars = array(
        'chr' => array(),
        'ord' => array(),
    );

    /**
     * Настройки типографа по умолчанию.
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
        'encoding' => self::MODE_NONE,

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
         * Используемые модули.
         *
         * @var string[]
         */
        'modules' => array(
            'code',
            'html',
            'nobr',
            'punct',
            'space',
            'symbol',
            'url',
        ),

        /**
         * Замена буквы 'ё' на 'е'.
         *
         * @var bool
         */
        'e-convert' => false,

        /**
         * Тип HTML документа.
         *
         * @var 'DOCTYPE_HTML4_STRICT'|'DOCTYPE_HTML4_TRANSITIONAL'|'DOCTYPE_HTML4_FRAMESET'|'DOCTYPE_XHTML1_STRICT'|
         *      'DOCTYPE_XHTML1_TRANSITIONAL'|'DOCTYPE_XHTML1_FRAMESET'|'DOCTYPE_XHTML11'|'DOCTYPE_XHTML5'
         */
        // 'html-doctype' => self::DOCTYPE_HTML5,

        /**
         * Вставлять &lt;br&gt; перед каждым переводом строки.
         *
         * @var bool
         */
        // 'nl2br' => true,
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 0,
        'B' => 40, // 5
        'C' => 0,
        'D' => 35,
        'E' => 0,
        'F' => 0,
    );

    /**
     * Версия типографа
     *
     * @var string
     */
    static private $version = '0.3';


    public $config_section;


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
     * Установливает значения параметров настроек по умолчанию,
     * затем установливает заданные значения параметров настроек.
     *
     * @param string|array $options Массив настроек или название секции в файле настроек.
     *                              По умолчанию используются настройки [default].
     *
     * @uses \Wheels\Typo\Module::__construct()
     */
    public function __construct($options = 'default')
    {
        $this->text = new Text();
        $this->config_section = (is_string($options) ? $options : 'default');

        parent::__construct($options);
    }

    /**
     * Проверка значения параметра (с возможной корректировкой).
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     */
    public function validateOption($name, &$value)
    {
        switch($name)
        {
            case 'charset' :
                if($value != self::AUTO)
                {
                    $value = mb_strtoupper($value);
                    $result = iconv($value, 'UTF-8', '');
                    if($result === false)
                        return self::throwException(Exception::E_OPTION_VALUE, "Неизвестная кодировка '$value'");
                }
            break;

            case 'encoding' :
                if($value != self::AUTO)
                {
                    if(!Utility::validateConst(get_called_class(), $value, 'MODE'))
                        return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный режим кодирования спецсимволов '$value'");
                }
            break;

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
     * echo $typo->process('Какой-то текст...');
     * </code>
     *
     * @param \Wheels\Typo\Text|string $text  Исходный текст.
     *
     * @return string Оттипографированный текст.
     */
    public function process($text)
    {
        if($text instanceof Text)
            // @error нельзя переопределять
            $this->text = $text;
        elseif($this->options['charset'] == self::AUTO)
        {
            $this->text->setText($text);
            $this->setOption('charset', $this->text->getEncoding());
        }
        else
        {
            $this->text->setText($text, $this->options['charset']);
        }

        // @todo: исправить ошибки повторного вызова
        // text->getCharset();
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
        $this->resetStage();
        do
        {
            $this->processStage();
        }
        while($this->setNextStage());

        // Восстанавливаем кодировку текста
        if($charset != $default_charset)
            $this->text->iconv($default_charset, $charset);
        mb_internal_encoding($int_encoding);

        return $this->text->getText();
    }

    /**
     * Клонирование объекта.
     */
    public function __clone()
    {
        return unserialize(serialize($this));
    }


    // --- Защищённые методы класса ---

    /**
     * Стадия A.
     */
    protected function stageA()
    {
        $rules = array(
            #A1 Убираем лишние пробелы в кодах символов
            '~(&(#\d+|[\da-z]+|#x[\da-f]+))\h+(?=\;)~i' => '$1',

            #A2 Добавляем недостающие точки с запятой в кодах символов
            '~(&#\d+)(?![\;\d])~' => '$1;',
            '~(&[\da-z]+)(?![\;\da-z])~i' => '$1;',
            '~(&#x[\da-f]+)(?![\;\da-f])~i' => '$1;',

            #A3 Замена буквы 'ё' на 'е'
            'e-convert' => array(
                'ё' => 'е',
                'Ё' => 'Е',
            ),
        );
        $this->applyRules($rules);

        $this->text->html_entity_decode(ENT_QUOTES | ENT_HTML401);

        if(!$this->options['html-in-enabled'])
        {
            $this->text->htmlspecialchars();
        }

        /*$chars = array(
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
             $this->text->replace($group, $replace);*/
    }

    /**
     * Стадия B.
     */
    protected function stageB()
    {
        $s =& $this->chr;

        if($this->options['encoding'] !== self::MODE_NONE)
        {
            $this->text->replace(array_values(self::$chars['chr']), array_values($this->chr));
        }

        $rules = array(
            #B1 Заменяем все неизвестные символы
            '~' . preg_quote($s['amp'], '~') . '(#\d+|[\da-z]+|#x[\da-f]+)\;~i' => $this->chr(65533),
        );
        $this->applyRules($rules);
    }

    /**
     * Стадия D.
     */
    protected function stageD()
    {
        $this->text->popStorage(self::REPLACER, self::INVISIBLE);
        $this->text->popStorage(self::REPLACER, self::VISIBLE);

        // Вставлять <br> перед каждым переводом строки
        $this->text->preg_replace('~\n|\&NewLine\;~', '<br />');
        ////if($this->options['nl2br'])
        //   $this->text->nl2br();
    }

    /**
     * Обработчик события изменения значения параметра.
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     */
    protected function onChangeOption($name, &$value)
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
                            $this->chr =& self::$chars['chr'];
                        break;
                        case self::MODE_CODES :
                            foreach(self::$chars['ord'] as $ent => $ord)
                                $this->chr[$ent] = sprintf('&#%u;', $ord);
                        break;
                        case self::MODE_HEX_CODES :
                            foreach(self::$chars['ord'] as $ent => $ord)
                                $this->chr[$ent] = sprintf('&#x%x;', $ord);
                        break;
                        case self::MODE_NAMES :
                            foreach(array_keys(self::$chars['chr']) as $ent)
                                $this->chr[$ent] = sprintf('&%s;', $ent);;
                        break;
                    }
                }
            break;
        }
    }

    /**
     * Возвращает символ по его коду.
     *
     * @param int $c Код символа.
     *
     * @return string|bool
     */
    protected function chr($c)
    {
        switch($this->options['encoding'])
        {
            case self::MODE_NONE :
                return Utility::chr($c);
            break;
            case self::MODE_CODES :
                return sprintf('&#%u;', $c);
            break;
            default :
                return sprintf('&#x%x;', $c);
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
     * @uses \Wheels\Typo::process()
     *
     * @return string Оттипографированный текст.
     */
    static public function staticProcess($text, array $options = array())
    {
        $typo = new self($options);

        return $typo->process($text);
    }
}

$chars = get_html_translation_table(HTML_ENTITIES, ENT_HTML401, 'UTF-8');
foreach($chars as $chr => $entitie)
{
    $name = substr($entitie, 1, strlen($entitie) - 2);
    Typo::$chars['ord'][$name] = Utility::ord($chr);
    Typo::$chars['chr'][$name] = $chr;
}
