<?php

namespace Wheels\Typo;

use Wheels\Typo;
use Wheels\Typo\Module;
use Wheels\Config;
use Wheels\Typo\Exception;

/**
 * Модуль.
 */
abstract class Module
{
    /**
     * Типограф, использующий данный модуль.
     *
     * @var \Wheels\Typo
     */
    public $typo;

    /**
     * Текст.
     *
     * @var \Wheels\Typo\Text
     */
    public $text;

    /**
     * Настройки.
     *
     * @var array
     */
//    protected $_options = array();

    /**
     * Описание конфигурации модуля.
     *
     * @var array
     */
    static protected $_config_schema = array(
        'options' => array(
            'modules' => array(
                'desc'    => 'Используемые модули',
                'type'    => 'string[]',
                'default' => array(),
                'inherit' => true,
            ),
        ),
    );

    /**
     * Область работы модуля.
     *
     * @var string[]
     */
    static protected $area = null;

    /**
     * Используемые модули.
     *
     * @var \Wheels\Typo\Module\Collection|\Wheels\Typo\Module[]
     */
    protected $_modules = array();

    /**
     * Текущая стадия.
     *
     * @var int
     */
    protected $_stage;

    /**
     * Приоритет выполнения стадий
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

    /**
     * Конфигурационный INI файл.
     *
     * @var \Wheels\Config
     */
    protected $_config;

    public $config_section;


    // --- Конструктор ---

    /**
     * Установливает значения параметров настроек по умолчанию,
     * затем установливает заданные значения параметров настроек.
     *
     * @param string|array $options Массив настроек или название секции в файле настроек.
     * @param \Wheels\Typo $typo           Типограф, использующий данный модуль.
     *
     * @uses \Wheels\Typo\Module::setOptions()
     * @uses \Wheels\Typo\Module::getDefaultOptions()
     */
    public function __construct($options = 'default', Typo $typo = null)
    {
        if(isset($typo))
        {
            $this->typo = $typo;
            $this->text =& $typo->text;
            $this->config_section = $typo->config_section;
//            $this->setConfigDir($typo->_config->getDirectory());
        }
        else
        {
            $this->config_section = is_string($options) ? $options : 'default';
            $this->setConfigDir(TYPO_CONFIG_DIR);
        }

        $schema = static::getConfigSchema();
        $this->_config = Config::create($schema);


        $this->setOptionsFromFile('default');

        $this->setOptions($options);
        foreach($this->getDefaultOptions() as $name => $value)
        {
            if(!array_key_exists($name, $this->_options))
                $this->setOption($name, $value);
        }
    }


    // --- Открытые методы класса ---

    /**
     * Возвращает массив значений параметров по умолчанию.
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->_config->getDefaultOptions();
    }

    /**
     * Проверка значения параметра (с возможной корректировкой).
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     */
    public function validateOption($name, &$value)
    {
        if(!$this->checkOptionExists($name))
            return self::throwException(Exception::E_OPTION_NAME, "Несуществующий параметр '$name'");

        // $scheme = static::getConfigSchema($name);

        switch($name)
        {
            case 'modules' :
                if((bool) $value == false)
                    $value = array();

                if(is_string($value))
                    $value = explode(',', $value);

                if(!is_array($value))
                    return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть строкой или массивом строк");

                foreach($value as &$module)
                {
                    if(!is_string($module))
                        return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть строкой или массивом строк");
                }

                foreach($value as &$module)
                {
                    $module = self::getModuleClassname($module);
                }
            break;

            default : $value = (bool) $value;
        }
    }

    /**
     * Установливает значение параметра настроек.
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     *
     * @throws \Wheels\Typo\Exception
     */
    public function setOption($name, $value)
    {
        $this->_config->setOptionValue($name, $value);
        $this->onChangeOption($name, $value);
    }

    public function setOptionsFromConfigFile($section = 'default')
    {
        $filename = $this->configFilename;
        $this->_config->setOptionsValuesFromFile($filename, $section);

        foreach($this->_modules as $module)
        {
            $module->setOptionsFromFile($section);
        }
    }

    /**
     * Установливает значения параметров настроек.
     *
     * @param string|array $options Массив настроек или название секции в файле настроек.
     *
     * @uses \Wheels\Typo\Module::setOption()
     *
     * @return void
     *
     * @throws \Wheels\Typo\Exception
     */
    public function setOptions(array $options)
    {
        $this->_config->setOptionsValues($options);
    }

    /**
     * @return \Wheels\Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @return \Wheels\Typo\Module\Collection|\Wheels\Typo\Module[]
     */
    public function getModules()
    {
        return $this->_modules;
    }

    /**
     * Устанавливает значения параметров по умолчанию.
     */
    public function setDefaultOptions()
    {
        foreach($this->getConfig()->getOptions() as $option) {
            $option->setValueDefault();
        }

        foreach($this->getModules() as $module) {
            $module->setDefaultOptions();
        }
    }

    public function setConfigDir($dir)
    {
        if(!is_dir($dir))
            return self::throwException(Exception::E_RUNTIME, "'$dir' не является директорией");
        if(!file_exists($dir))
            return self::throwException(Exception::E_RUNTIME, "Директория '$dir' не существует");

        $filename = strtolower(get_called_class()) . '.ini';
        $filename = str_replace(strtolower('Wheels' . DS), '', $filename);
        $filename = str_replace(strtolower('Typo' . DS . 'Module' . DS), '', $filename);
        $filename = $dir . DS . $filename;

        $this->_config = new Config($filename);
    }

    /**
     * Возвращает значение параметра.
     *
     * @param string $name  Название параметра.
     *
     * @throws \Wheels\Typo\Exception
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if(!$this->checkOptionExists($name))
            return self::throwException(Exception::E_OPTION_NAME, "Несуществующий параметр '$name'");

        return $this->_options[$name];
    }

    /**
     * Возвращает приоритет выполнения текущей стадии.
     *
     * @return int
     */
    public function getOrder()
    {
        $stages = self::getStages();
        $key = $stages[$this->_stage];

        return static::$_order[$key];
    }

    /**
     * Возвращает модуль с заданным именем.
     *
     * @param string $name  Имя модуля (полное или частичное имя класса модуля).
     *
     *
     * @return \Wheels\Typo\Module Модуль с заданным именем либо NULL, если модуль
     *                             с таким именем класса не найден среди используемых.
     */
    public function getModule($name)
    {
        $name = str_replace('/', '\\', trim($name));

        if(substr($name, 0, 1) !== '\\')
            $name = '\\' . $name;

        $name = preg_quote($name, '~');
        foreach($this->_modules as $key => $module)
        {
            if(preg_match('~' . $name . '$~i', '\\' . $key))
                return $module;
        }

        return NULL;
    }

    /**
     * Добавляет модуль.
     *
     * @param \Wheels\Typo\Module|string $name  Модуль или его имя.
     * @param string|array $options
     *
     * @throws \Wheels\Typo\Exception
     */
    public function addModule($name, $options = 'default')
    {
        if(is_object($module = $name) && ($module instanceof Wheels\Typo\Module))
        {
            $classname = get_class($module);
            if(!array_key_exists($classname, $this->_modules))
            {
                $module->setOptions($options);
                $this->_modules[$classname] = $module;
                // $module->setTypo(...)
            }
            return;
        }

        $classname = self::getModuleClassname($name);
        if(!class_exists($classname))
        {
            return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный модуль '$name' (класс $classname не найден)");
        }
        elseif(!array_key_exists($classname, $this->_modules))
        {
            if(is_subclass_of($classname, __CLASS__))
            {
                $typo = ($this instanceof Typo) ? $this : $this->typo;
                $this->_modules[$classname] = new $classname($options, $typo);
            }
            else
                return self::throwException(Exception::E_OPTION_VALUE, "Класс $classname не является наследником класса " . __CLASS__);
        }
        else
            return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный модуль '$name' (класс $classname не найден)");
    }

    /**
     * Удаляет модуль.
     *
     * @param string $name  Название модуля.
     *
     * @throws \Wheels\Typo\Exception
     *
     * @return void
     */
    public function removeModule($name)
    {
        $classname = self::getModuleClassname($name);
        if(!class_exists($classname))
        {
            return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный модуль '$name' (класс " . $classname . " не найден)");
        }
        elseif(array_key_exists($classname, $this->_modules))
        {
            $this->_options['modules'] = array_diff($this->_options['modules'], array($classname));
            unset($this->_modules[$classname]);
        }
    }

    public function resetStage()
    {
        return $this->setStage(0);
    }

    public function setNextStage()
    {
        return $this->setStage($this->_stage + 1);
    }

    /**
     * Устанавливает стадию работы, сортирует используемые модули по приоритетам выполнения.
     *
     * @param int $stage Стадия работы.
     *
     * @return bool
     */
    public function setStage($stage)
    {
        $count = count(self::getStages());
        if($stage < 0 || $stage >= $count)
            return false;

        $this->_stage = $stage;

        foreach($this->_modules as $module)
            $module->setStage($stage);

        uasort($this->_modules, function(Module $a, Module $b) {
            return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
        });

        return true;
    }

    /**
     * Запускает выполнение текущей стадии.
     *
     * @uses \Wheels\Typo\Module::getStageMethod()
     * @uses \Wheels\Typo\Module::getOrder();
     *
     * @return void
     */
    public function processStage()
    {
        $method = $this->getStageMethod();

        $fl = true;
        foreach($this->_modules as $module)
        {
            if($fl && $module->getOrder() >= $this->getOrder())
            {
                if(method_exists($this, $method) && $this->checkTextType())
                {
                    $this->$method();
                    # echo get_called_class() . '::' . $method . '<br>';
                }
                $fl = false;
            }
            $module->processStage();
        }

        if($fl && method_exists($this, $method) && $this->checkTextType())
        {
            $this->$method();
            # echo get_called_class() . '::' . $method . '<br>';
        }
    }

    /**
     * Применяет правила к тексту.
     *
     * @param array $rules      Набор правил.
     * @param array $helpers    Вспомогательные элементы регулярных выражений.
     *
     * @return void|string
     */
    public function applyRules(array $rules, array $helpers = array(), $text = null)
    {
        $patterns = array();
        $replaces = array();
        foreach($rules as $key => $value)
        {
            if(is_array($value) && array_key_exists($key, $this->_options))
            {
                if($this->_options[$key])
                {
                    $patterns = array_merge($patterns, array_keys($value));
                    $replaces = array_merge($replaces, array_values($value));
                }
            }
            else
            {
                $patterns[] = $key;
                $replaces[] = $value;
            }
        }

        self::pregHelpers($patterns, $helpers);

        for($i = 0, $count = sizeof($patterns); $i < $count; $i++)
        {
            if(is_callable($replaces[$i]))
            {
                if(isset($text))
                    $text = preg_replace_callback($patterns[$i], $replaces[$i], $text);
                else
                    $this->text->preg_replace_callback($patterns[$i], $replaces[$i]);
            }
            elseif(is_array($replaces[$i]))
            {
                $_this = $this;
                $callback = function($matches) use($_this, $replaces, $i, $helpers) {
                    return $_this->applyRules($replaces[$i], $helpers, $matches[0]);
                };
                $this->text->preg_replace_callback($patterns[$i], $callback);
            }
            else
            {
                if(isset($text))
                    $text = preg_replace($patterns[$i], $replaces[$i], $text);
                elseif(mb_substr($patterns[$i], 0, 1) === '~')
                    $this->text->preg_replace($patterns[$i], $replaces[$i]);
                else
                    $this->text->replace($patterns[$i], $replaces[$i]);
            }
        }

        if(isset($text))
            return $text;
    }


    // --- Защищённые методы класса ---

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
            case 'modules' :
                $this->_modules = array();
                foreach($value as $module)
                {
                    $this->addModule($module, $this->config_section);
                }
            break;
        }
    }

    protected function checkTextType()
    {
        $class = get_called_class();
        return (is_null($class::$area) || in_array($this->text->type, $class::$area));
    }

    /**
     * Проверяет существование параметра настроек.
     *
     * @param string $name  Название параметра.
     *
     * @return bool Вовзращает true, если параметр с таким именем существует, иначе - false.
     */
    protected function checkOptionExists($name)
    {
        return array_key_exists($name, $this->getDefaultOptions());
    }

    /**
     * Вовзращает имя метода текущей стадии.
     *
     * @return string
     */
    protected function getStageMethod()
    {
        $stages = self::getStages();
        $name = $stages[$this->_stage];
        return 'stage' . $name;
    }


    // --- Статические методы класса ---

    /**
     * Возвращает массив имён всех стадий.
     *
     * @return array
     */
    static protected function getStages()
    {
        return array_keys(self::$_order);
    }

    /**
     * Раскрывает вспомогательные элементы регулярных выражений.
     *
     * @staticvar array $std_helpers    Стандартные вспомогательные элементы регулярных выражений.
     *
     * @param string|string[] $pattern  Регулярное выражение или массив регулярных выражений.
     * @param array $helpers            Вспомогательные элементы регулярных выражений.
     *
     * @return void
     */
    static protected function pregHelpers(&$pattern, array $helpers = array())
    {
        static $std_helpers = array(
            // Буквы
            '{a}' => '[a-zA-Zа-яА-ЯёЁ]',

            // Видимый элемент
            '{b}' => '(?:\[\[\[\w+\]\]\])',

            // Единицы измерения
            // http://ru.wikipedia.org/wiki/%D0%9F%D1%80%D0%B8%D1%81%D1%82%D0%B0%D0%B2%D0%BA%D0%B8_%D0%A1%D0%98
            // @todo: Отменить регистронезависимость
            '{m}' => '(?:(?:[изафпнмсдгкМГТПЭЗИ]|мк|да)?(?:[бгмлтБВН]|Па|Гц|байт|бит|флоп\/?с)|(?:[yzafpnmcdhkMGTPEZY\xB5]|da)?(?:[bgmlLtBVN]|Pa|Hz|byte|bit|FLOPS|flop\/?s))',

            // Знаки препинания
            '{p}' => '[!?:;,.]',

            // Невидимый элемент
            '{t}' => '(?:\{\{\{\w+\}\}\})',
        );

        if(is_array($pattern))
            $patterns = $pattern;
        else
            $patterns = array($pattern);

        $helpers = array_merge($std_helpers, $helpers);
        $helpers_keys = array_keys($helpers);
        $helpers_values = array_values($helpers);

        foreach($patterns as &$p)
        {
            $count = 0;
            do
            {
                $p = str_replace($helpers_keys, $helpers_values, $p, $count);
            } while($count != 0);
        }

        if(is_array($pattern))
            $pattern = $patterns;
        else
            $pattern = $patterns[0];
    }

    /**
     * Возвращает название класса по имени модуля.
     *
     * @param string $name  Название модуля.
     *
     * @return string
     */
    static protected function getModuleClassname($name)
    {
        if(class_exists($name))
            return $name;

        $classname = '\\' . __CLASS__;
        foreach(explode('/', $name) as $part)
        {
            $classname .= '\\' . ucfirst($part);
        }

        return $classname;
    }

    static public function getConfigSchema()
    {
        if(!array_key_exists('extends', static::$_config_schema) || !static::$_config_schema['extends'])
        {
            foreach(static::$_config_schema['options'] as $n => $schema)
            {
                if(!array_key_exists('inherit', $schema))
                    static::$_config_schema['options'][$n]['inherit'] = FALSE;
            }

            /** @var \Wheels\Typo $parent */
            $parent = get_parent_class(get_called_class());

            if($parent !== FALSE)
            {
                $parent_schema = $parent::getConfigSchema();
                foreach($parent_schema['options'] as $n => $schema)
                {
                    if($schema['inherit'])
                    {
                        if(!array_key_exists($n, static::$_config_schema['options']))
                            static::$_config_schema['options'][$n] = array();
                        static::$_config_schema['options'][$n] = array_merge($schema, static::$_config_schema['options'][$n]);
                    }
                }
            }

            static::$_config_schema['extends'] = true;
        }

        return static::$_config_schema;
    }

    /**
     * Выбрасывает исключение.
     *
     * @param int             $code       Код состояния.
     * @param string          $message    Сообщение.
     * @param \Wheels\Typo\Exception $previous   Предыдущее исключение.
     *
     * @throws \Wheels\Typo\Exception
     *
     * @return void
     */
    static public function throwException($code = Exception::E_UNKNOWN, $message = null, Exception $previous = null)
    {
        if(isset($message))
            throw new Exception($message, $code, $previous);
        else
        {
            $message = Exception::getMessageByCode($code);
            throw new Exception($message, $code, $previous);
        }
    }
}