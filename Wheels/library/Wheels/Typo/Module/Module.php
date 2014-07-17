<?php

/**
 * Wheels Library
 *
 * @category Wheels
 * @package  Wheels\Typo
 */

namespace Wheels\Typo\Module;

use Wheels\Typo\Typo;
use Wheels\Typo\AbstractTypo;
use Wheels\Config;

/**
 * Модуль типографа.
 */
abstract class Module extends AbstractTypo
{
    /**
     * Типограф, использующий данный модуль.
     *
     * @var \Wheels\Typo\Typo
     */
    protected $_typo;

    /**
     * Вспомогательные элементы регулярных выражений.
     *
     * @var array
     */
    static protected $_helpers = array();

    /**
     * Область работы модуля.
     *
     * @var string[]
     */
    static protected $area = null;

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static protected $_order = array(
        'A' => 0,
        'B' => 0,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Заменители ---

    const REPLACER = 'X';

    /** Видимый элемент. */
    const VISIBLE = '[[[%s%u]]]';

    /** Невидимый элемент. */
    const INVISIBLE = '{{{%s%u}}}';


    // --- Открытые методы ---

    public function __construct(Typo $parent, array $options = array())
    {
        $this->_typo = $parent;

        parent::__construct($options);
    }

    /**
     * Возвращает типограф, использующий данный модуль.
     *
     * @return \Wheels\Typo\Typo
     */
    public function getTypo()
    {
        return $this->_typo;
    }

    /**
     * Возвращает приоритет выполнения для стадии.
     *
     * @param $stage
     *
     * @return int
     */
    public function getOrder($stage)
    {
        return static::$_order[$stage];
    }

    static public function addHelpers(array $helpers)
    {
        static::$_helpers = array_merge(static::$_helpers, $helpers);
    }

    public function applyRulesReplace(array $rules)
    {
        $this->getTypo()->getText()->replace(array_keys($rules), array_values($rules));
    }

    // @todo: перенести в Text
    public function applyRulesPregReplace(array $rules, $text = null)
    {
        $patterns = array();
        $replaces = array();
        foreach ($rules as $key => $value) {
            $patterns[] = $key;
            $replaces[] = $value;
        }

        $patterns = array_keys($rules);
        $replaces = array_values($rules);

        static::pregHelpers($patterns);

        for ($i = 0, $count = sizeof($patterns); $i < $count; $i++) {
            if (is_callable($replaces[$i])) {
                if (isset($text)) {
                    $text = preg_replace_callback($patterns[$i], $replaces[$i], $text);
                }
                else {
                    $this->getTypo()->getText()->preg_replace_callback($patterns[$i], $replaces[$i]);
                }
            } elseif (is_array($replaces[$i])) {
                $_this = $this;
                $callback = function ($matches) use ($_this, $replaces, $i) {
                    return $_this->applyRulesPregReplace($replaces[$i], $matches[0]);
                };
                $this->getTypo()->getText()->preg_replace_callback($patterns[$i], $callback);
            } else {
                if (isset($text))
                    $text = preg_replace($patterns[$i], $replaces[$i], $text);
                else
                    $this->getTypo()->getText()->preg_replace($patterns[$i], $replaces[$i]);
            }
        }

        if (isset($text)) {
            return $text;
        }
    }

    /**
     * Применяет правила к тексту.
     *
     * @param array  $rules   Набор правил.
     * @param array  $helpers Вспомогательные элементы регулярных выражений.
     * @param string $text
     *
     * @return void|string
     */
    public function applyRules(array $rules, array $helpers = array(), $text = null)
    {
        $patterns = array();
        $replaces = array();
        foreach ($rules as $key => $value) {
            $patterns[] = $key;
            $replaces[] = $value;
        }

        self::pregHelpers($patterns, $helpers);

        for ($i = 0, $count = sizeof($patterns); $i < $count; $i++) {
            if (is_callable($replaces[$i])) {
                if (isset($text))
                    $text = preg_replace_callback($patterns[$i], $replaces[$i], $text);
                else
                    $this->getTypo()->getText()->preg_replace_callback($patterns[$i], $replaces[$i]);
            } elseif (is_array($replaces[$i])) {
                $_this = $this;
                $callback = function ($matches) use ($_this, $replaces, $i, $helpers) {
                    return $_this->applyRules($replaces[$i], $helpers, $matches[0]);
                };
                $this->getTypo()->getText()->preg_replace_callback($patterns[$i], $callback);
            } else {
                if (isset($text))
                    $text = preg_replace($patterns[$i], $replaces[$i], $text);
                elseif (mb_substr($patterns[$i], 0, 1) === '/') // @todo: regexp???
                    $this->getTypo()->getText()->preg_replace($patterns[$i], $replaces[$i]);
                else
                    $this->getTypo()->getText()->replace($patterns[$i], $replaces[$i]);
            }
        }

        if (isset($text))
            return $text;
    }


    // --- Защищённые методы ---

//    /**
//     * Задаёт модули.
//     *
//     * @param \Wheels\Typo\Module\Module[] $modules Массив модулей.
//     *
//     * @return void Этот метод не возвращает значения после выполнения.
//     */
//    public function setModules(array $modules)
//    {
//        $this->getModules()->clear();
//        $this->addModules($modules);
//    }
//
//    /**
//     * Добавляет модуль.
//     *
//     * @param \Wheels\Typo\Module\Module $module Модуль.
//     *
//     * @return void Этот метод не возвращает значения после выполнения.
//     */
//    public function addModule(Module $module)
//    {
//        $this->_modules[] = $module;
//    }
//
//    /**
//     * Добавляет модули.
//     *
//     * @param \Wheels\Typo\Module\Module[] $modules Массив модулей.
//     *
//     * @return void Этот метод не возвращает значения после выполнения.
//     */
//    public function addModules(array $modules)
//    {
//        foreach ($modules as $module)
//            $this->addModule($module);
//    }

    protected function checkTextType()
    {
        $class = get_called_class();
        return (is_null($class::$area) || in_array($this->getTypo()->getText()->type, $class::$area));
    }

    /**
     * Раскрывает вспомогательные элементы регулярных выражений.
     *
     * @staticvar array $std_helpers    Стандартные вспомогательные элементы регулярных выражений.
     *
     * @param string|string[] $pattern Регулярное выражение или массив регулярных выражений.
     *
     * @return void
     */
    static protected function pregHelpers(&$pattern)
    {
        static $std_helpers = array(
            // Буквы
            '{a}' => '[a-zа-яё]',

            // Видимый элемент
            '{b}' => '(?:\[\[\[\w+\]\]\])',

            // Единицы измерения
            // http://ru.wikipedia.org/wiki/%D0%9F%D1%80%D0%B8%D1%81%D1%82%D0%B0%D0%B2%D0%BA%D0%B8_%D0%A1%D0%98
            // @todo: Отменить регистронезависимость
            '{m}' => '(?:(?:[изафпнмсдгкМГТПЭЗИ]|мк|да)?(?:[бгмлтБВН]|Па|Гц|байт|бит|флоп\/?с)|(?:[yzafpnmcdhkMGTPEZY\xB5]|da)?(?:[bgmlLtBVN]|Pa|Hz|byte|bit|FLOPS|flop\/?s))',

            // Невидимый элемент
            '{t}' => '(?:\{\{\{\w+\}\}\})',
        );

        if (is_array($pattern)) {
            $patterns = $pattern;
        } else {
            $patterns = array($pattern);
        }

        $helpers = array_merge($std_helpers, static::$_helpers);
        $helpers_keys = array_keys($helpers);
        $helpers_values = array_values($helpers);

        foreach ($patterns as &$p) {
            $count = 0;
            do {
                $p = str_replace($helpers_keys, $helpers_values, $p, $count);
            } while ($count != 0);
        }

        if (is_array($pattern))
            $pattern = $patterns;
        else
            $pattern = $patterns[0];
    }

    /**
     * Возвращает название класса по имени модуля.
     *
     * @param string $name Название модуля.
     *
     * @return string Полное имя класса модуля.
     */
    static public function getModuleClassname($name)
    {
        static $aliases = array(
            'align'          => '\Wheels\Typo\Module\Align\Align',
            'code'           => '\Wheels\Typo\Module\Code\Code',
            'core'           => '\Wheels\Typo\Module\Core\Core',
            'html'           => '\Wheels\Typo\Module\Html\Html',
            'nobr'           => '\Wheels\Typo\Module\Nobr\Nobr',
            'punct'          => '\Wheels\Typo\Module\Punct\Punct',
            'space'          => '\Wheels\Typo\Module\Space\Space',
            'symbol'         => '\Wheels\Typo\Module\Symbol\Symbol',
            'url'            => '\Wheels\Typo\Module\Url\Url',
            'emoticon/skype' => '\Wheels\Typo\Module\Emoticon\Skype\Skype',
        );

        $classname = $name;
        if (substr($classname, 0, 1) !== '\\') {
            $classname = '\\' . $classname;
        }

        if (!class_exists($classname)) {
            $name = strtolower($name);

            if (array_key_exists($name, $aliases)) {
                $classname = $aliases[$name];
            }
        }

        return $classname;
    }

    /**
     * Выбрасывает исключение.
     *
     * @param int                    $code     Код состояния.
     * @param string                 $message  Сообщение.
     * @param \Wheels\Typo\Exception $previous Предыдущее исключение.
     *
     * @throws \Wheels\Typo\Exception
     *
     * @return void
     */
//    static public function throwException($code = Exception::E_UNKNOWN, $message = null, Exception $previous = null)
//    {
//        if (isset($message))
//            throw new Exception($message, $code, $previous);
//        else {
//            $message = Exception::getMessageByCode($code);
//            throw new Exception($message, $code, $previous);
//        }
//    }
}