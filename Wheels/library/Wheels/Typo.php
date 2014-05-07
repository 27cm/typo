<?php

namespace Wheels;

use Wheels\Typo\Module;
use Wheels\Typo\Text;
use Wheels\Typo\Utility;
use Wheels\Typo\Exception;

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    trigger_error('Для работы типограф требуется версия php 5.3.0 или выше', E_USER_ERROR);
}

if (!extension_loaded('mbstring')) {
    $ext = ((substr(PHP_OS, 0, 3) == 'WIN') ? 'dll' : 'so');
    if (!ini_get('enable_dl') || !dl('mbstring' . $ext)) {
        trigger_error('Для работы типографа требуется расширение mbstring', E_USER_ERROR);
    }
}

/**
 * Каталог с библиотекой Wheels\Typo.
 */
define('TYPO_DIR', dirname(__FILE__));

/**
 * Директория с файлами конфигурации.
 */
define('TYPO_CONFIG_DIR', realpath(TYPO_DIR . DS . 'config'));
if (!TYPO_CONFIG_DIR) {
    trigger_error('Директория с файлами конфигурации типографа не найдена', E_USER_ERROR);
}

require_once TYPO_DIR . DS . 'Typo' . DS . 'functions.php';

/**
 * Типограф.
 *
 * @version 0.3 2014-02-16
 */
class Typo extends Module
{
    /**
     * Коды символов.
     *
     * @link http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references
     *
     * @var array
     */
    static protected $_chars
        = array(
            'chr' => array(),
            'ord' => array(),
        );

    /**
     * {@inheritDoc}
     */
    static protected $_config_schema;

    /**
     * @see \Wheels\Typo\Module::$order
     */
    static protected $_order
        = array(
            'A' => 5,
            'B' => 40,
            'C' => 0,
            'D' => 35,
            'E' => 0,
            'F' => 0,
        );

    /**
     * Версия типографа.
     *
     * @var string
     */
    static private $version = '0.3';


    // --- Режимы кодирования спецсимволов ---

    /** Не кодировать. */
    const MODE_NONE = 'MODE_NONE';

    /** В виде имён. */
    const MODE_NAMES = 'MODE_NAMES';

    /** В виде кодов. */
    const MODE_CODES = 'MODE_CODES';

    /** В виде шестнадцатеричных кодов. */
    const MODE_HEX_CODES = 'MODE_HEX_CODES';


    // --- Заменители ---

    /** Элемент. */
    const REPLACER = 'E';

    /** Видимый элемент. */
    const VISIBLE = '[[[%s%u]]]';

    /** Невидимый элемент. */
    const INVISIBLE = '{{{%s%u}}}';


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
        // @todo: вынести хранилище из text, тогда Text() не нужно будет создавать заранее
        $this->text = new Text();

        parent::__construct($options);
    }

    /**
     * @see \Wheels\Typo\Module::validateOption()
     */
    public function validateOption($name, &$value)
    {

    }

    /**
     * Типографирует текст.
     *
     * <code>
     * $typo = new Typo;
     * echo $typo->process('Какой-то текст...');
     * </code>
     *
     * @param \Wheels\Typo\Text|string $text Исходный текст.
     *
     * @return string Оттипографированный текст.
     */
    public function process($text, $options = null)
    {
        // @todo: Перед началом выполнения необходимо заблокировать все конфиги
        // @todo: Все модули должны быть только в классе типографа, а сам типограф не должен являться модулем
        // @todo: Модули должны определять порядок выполнения путём проверки условий, а не с помощью номеров

        if (isset($options)) {
            $this->setOptions($options);
        }

        if ($text instanceof Text) // @todo нельзя переопределять
        {
            $this->text = $text;
        } elseif ($this->_options['charset'] == self::AUTO) {
            $this->text->setText($text);
            $this->setOption('charset', $this->text->getEncoding());
        } else {
            $this->text->setText($text, $this->_options['charset']);
        }

        // @todo: исправить ошибки повторного вызова
        // text->getCharset();

        $charset = $this->_options['charset'];
        $int_encoding = mb_internal_encoding();
        $default_charset = 'UTF-8';

        // Меняем кодировку текста
        mb_internal_encoding($default_charset);
        if ($charset != $default_charset) {
            $this->text->iconv($charset, $default_charset);
        }

        // Выполнение всех стадий
        $this->resetStage();
        do {
            $this->processStage();
        } while ($this->setNextStage());

        // Восстанавливаем кодировку текста
        if ($charset != $default_charset) {
            $this->text->iconv($default_charset, $charset);
        }
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
            '~(&(#\d+|[\da-z]+|#x[\da-f]+))\h+(?=\;)~iu' => '$1',

            #A2 Добавляем недостающие точки с запятой в кодах символов
            '~(&#\d+)(?![\;\d])~'                        => '$1;',
            '~(&[\da-z]+)(?![\;\da-z])~i'                => '$1;',
            '~(&#x[\da-f]+)(?![\;\da-f])~i'              => '$1;',

            #A3 Замена буквы 'ё' на 'е'
            'e-convert'                                  => array(
                'ё' => 'е',
                'Ё' => 'Е',
            ),
        );
        $this->applyRules($rules);

        $this->text->html_entity_decode(ENT_QUOTES);

        $rules = array(
            #A1 Замена всех неизвестных символов на &#65533;
            '~&(#\d+|[\da-z]+|#x[\da-f]+)\;~i' => Utility::chr(65533),
        );
        $this->applyRules($rules);

//        if(!$this->options['html-in-enabled'])
//        {
//            $this->text->htmlspecialchars();
//        }
    }

    /**
     * Стадия B.
     */
    protected function stageB()
    {
        if ($this->_options['encoding'] !== self::MODE_NONE) {
            $replace = array();
            switch ($this->_options['encoding']) {
                case self::MODE_CODES :
                    foreach (self::getChars('ord') as $ent => $ord) {
                        $replace[$ent] = sprintf('&#%u;', $ord);
                    }
                    break;
                case self::MODE_HEX_CODES :
                    foreach (self::getChars('ord') as $ent => $ord) {
                        $replace[$ent] = sprintf('&#x%x;', $ord);
                    }
                    break;
                case self::MODE_NAMES :
                    foreach (array_keys(self::getChars('chr')) as $ent) {
                        $replace[$ent] = sprintf('&%s;', $ent);
                    };
                    break;
            }

            // @todo: Заменить все символы, не поддерживаемый выходной кодировкой на HTML-сущности

            $search = self::getChars('chr');
            unset($search['amp']);
            unset($replace['amp']);
            $this->text->replace(array_values($search), array_values($replace));
        }
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
     * @see \Wheels\Typo\Module::onChangeOption()
     */
    protected function onChangeOption($name, &$value)
    {
        switch ($name) {
            case 'encoding' :
                // ...
                break;

            default :
                Module::onChangeOption($name, $value);
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
        switch ($this->_options['encoding']) {
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
     * @param string $text    Исходный текст.
     * @param array  $options Ассоциативный массив ('название параметра' => 'значение').
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

    /**
     *
     */
    static public function getChars($mode = null)
    {
        static $fl = true;
        if ($fl) {
            $chars = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES /*ENT_HTML401*/, 'UTF-8');
            foreach ($chars as $chr => $entitie) {
                if (preg_match('/&\w+;/', $entitie)) {
                    $name = substr($entitie, 1, strlen($entitie) - 2);
                    Typo::$_chars['ord'][$name] = Utility::ord($chr);
                    Typo::$_chars['chr'][$name] = $chr;
                }
            }
            $fl = false;
        }

        return (isset($mode) ? self::$_chars[$mode] : self::$_chars);
    }
}
