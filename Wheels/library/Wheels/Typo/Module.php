<?php

namespace Wheels\Typo;

use Wheels\Typo;
use Wheels\Typo\Module;
use Wheels\Typo\Config;
use Wheels\Typo\Exception;

/**
 * Модуль.
 */
abstract class Module
{
    /**
     * Типограф, использующий данный модуль.
     *
     * @var \Typo
     */
    public $typo;

    /**
     * Текст.
     *
     * @var \Typo\Text
     */
    public $text;

    /**
     * Настройки.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    // @todo: static
    protected $default_options = array();

    /**
     * Область работы модуля.
     *
     * @var string[]
     */
    static protected $area = null;

    /**
     * Используемые модули.
     *
     * @var \Typo\Module[]
     */
    protected $modules = array();

    /**
     * Текущая стадия.
     *
     * @var int
     */
    protected $stage;

    /**
     * Приоритет выполнения стадий
     *
     * @var array
     */
    static public $order = array(
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
     * @var \Typo\Config
     */
    protected $config;


    // --- Конструктор ---

    /**
     * Установливает значения параметров настроек по умолчанию,
     * затем установливает заданные значения параметров настроек.
     *
     * @param string|array $options Массив настроек или название секции в файле настроек.
     * @param \Wheels\Typo $typo           Типограф, использующий данный модуль.
     *
     * @uses \Wheels\Typo\Module::setOptions()
     */
    public function __construct($options = 'default', Typo $typo = null)
    {
        if(isset($typo))
        {
            $this->typo = $typo;
            $this->text =& $typo->text;
        }

        // @todo: может перенести
        $this->setConfigDir(TYPO_CONFIG_DIR);

        $this->setOptions($options);
        foreach($this->default_options as $name => $value)
        {
            if(!array_key_exists($name, $this->options))
                $this->setOption($name, $value);
        }
    }


    // --- Открытые методы класса ---

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

        switch($name)
        {
            case 'modules' :
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
        $name = strtolower($name);

        if(!array_key_exists($name, $this->default_options))
            return self::throwException(Exception::E_OPTION_NAME, "Несуществующий параметр '$name'");

        $this->validateOption($name, $value);

        $this->options[$name] = $value;
        $this->onChangeOption($name, $value);
    }

    /**
     * Установливает значения параметров настроек.
     *
     * @param string|array $options Массив настроек или название секции в файле настроек.
     *
     * @uses \Wheels\Typo\Module::setOption()
     *
     * @throws \Wheels\Typo\Exception
     *
     * @return void
     */
    public function setOptions($options)
    {
        if(is_string($options))
        {
            $section = $options;

            if($this->config->sectionExists($section))
                $options = $this->config->getSection($section);
            else
                return;
        }

        if(is_array($options))
        {
            $exception = null;

            foreach($options as $name => $value)
            {
                try
                {
                    $this->setOption($name, $value);
                }
                catch(Exception $e)
                {
                    if(isset($exception))
                        $exception = new Exception($e->getMessage(), $e->getCode(), $exception);
                    else
                        $exception = new Exception($e->getMessage(), $e->getCode());
                }
            }

            if(isset($exception))
                throw $exception;
        }
    }

    /**
     * Установливает значения параметров настроек по умолчанию.
     *
     * @uses \Wheels\Typo\Module::setOptions()
     */
    public function setDefaultOptions()
    {
        $this->setOptions($this->default_options);
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

        $this->config = new Config($filename);
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

        return $this->options[$name];
    }

    /**
     * Возвращает приоритет выполнения текущей стадии.
     *
     * @return int
     */
    public function getOrder()
    {
        $class = get_called_class();
        $stages = self::getStages();
        $key = $stages[$this->stage];

        return $class::$order[$key];
    }

    /**
     * Добавляет модуль.
     *
     * @param string $name          Название класса, либо абсолютное или относительное (например ./module/name) название модуля.
     * @param string|array $options
     *
     * @throws \Wheels\Typo\Exception
     */
    public function addModule($name, $options = 'default')
    {
        $classname = self::getModuleClassname($name);
        if(!class_exists($classname))
        {
            return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный модуль '$name' (класс $classname не найден)");
        }
        elseif(!array_key_exists($classname, $this->modules))
        {
            // @todo: fix \\
            if('\\' . $classname instanceof Wheels\Typo\Module)
            {
                $typo = ($this instanceof Typo) ? $this : $this->typo;
                $this->modules[$classname] = new $classname($options, $typo);
            }
            else
                return self::throwException(Exception::E_OPTION_VALUE, "Класс $classname не является модулем");
        }
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
        elseif(array_key_exists($classname, $this->modules))
        {
            $this->options['modules'] = array_diff($this->options['modules'], array($classname));
            unset($this->modules[$classname]);
        }
    }

    public function resetStage()
    {
        return $this->setStage(0);
    }

    public function setNextStage()
    {
        return $this->setStage($this->stage + 1);
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

        $this->stage = $stage;

        foreach($this->modules as $module)
            $module->setStage($stage);

        uasort($this->modules, function(Module $a, Module $b) {
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
        foreach($this->modules as $module)
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
            if(is_array($value) && array_key_exists($key, $this->options))
            {
                if($this->options[$key])
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
                $typo = ($this instanceof Typo) ? $this : $this->typo;

                $this->modules = array();
                foreach($value as $module)
                {
                    $this->addModule($module, $typo->config_section);
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
        return array_key_exists($name, $this->default_options);
    }

    /**
     * Вовзращает имя метода текущей стадии.
     *
     * @return string
     */
    protected function getStageMethod()
    {
        $stages = self::getStages();
        $name = $stages[$this->stage];
        return 'stage' . $name;
    }


    // --- Статические методы класса ---

    /**
     * Вовзвращает массив имён всех стадий.
     *
     * @return array
     */
    static protected function getStages()
    {
        return array_keys(self::$order);
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

            // Меры длинны
            '{m}' => '(?:[кдсмнпфази]?м|мкм)',

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

        $classname = __CLASS__;
        foreach(explode('/', $name) as $part)
        {
            $classname .= '\\' . ucfirst($part);
        }

        return $classname;
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