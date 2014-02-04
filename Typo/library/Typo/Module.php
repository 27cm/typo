<?php

namespace Typo;

use Typo;
use Typo\Module;
use Typo\Exception;

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
    protected $default_options = array();

    /**
     * Используемые модули.
     *
     * @var \Typo\Module[]
     */
    protected $modules = array();

    /**
     * Текущая стадия.
     *
     * @var string
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


    // --- Конструктор ---

    /**
     * Установливает значения параметров настроек по умолчанию,
     * затем установливает заданные значения параметров настроек.
     *
     * @param array $options    Ассоциативный массив ('название параметра' => 'значение').
     * @param \Typo $typo       Типограф, использующий данный модуль.
     *
     * @uses \Typo\Module::setDefaultOptions()
     * @uses \Typo\Module::setOption()
     */
    public function __construct(array $options = array(), Typo $typo = null)
    {
        if(isset($typo))
        {
            $this->typo = $typo;
            $this->text =& $typo->text;
        }

        $this->setDefaultOptions();
        $this->setOptions($options);
    }


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
        if(!$this->checkOptionExists($name))
            return self::throwException(self::E_OPTION_NAME, "Несуществующий параметр '$name'");

        switch($name)
        {
            // Модули
            case 'modules' :
                if(is_string($value))
                    $value = explode(',', $value);

                if(is_array($value))
                {
                    $exception = null;

                    $this->modules = array();
                    foreach($value as $module)
                    {
                        if(!is_string($module))
                            return self::throwException(Exception::E_OPTION_TYPE, "Значение параметра '$name' должно быть строкой или массивом строк");

                        $classname = self::getModuleClassname($module);
                        if(!class_exists($classname))
                        {
                            $message = "Неизвестный модуль '$module' (класс " . $classname . " не найден)";
                            if(isset($exception))
                                $exception = new Exception($message, Exception::E_OPTION_VALUE, $exception);
                            else
                                $exception = new Exception($message, Exception::E_OPTION_VALUE);
                        }
                        else
                        {
                            $typo = ($this instanceof Typo) ? $this : $this->typo;

                            $this->modules[] = new $classname(array(), $typo);
                        }
                    }

                    if(isset($exception)) throw $exception;
                }
                else
                    return self::throwException(self::E_OPTION_TYPE, "Значение параметра '$name' должно быть строкой или массивом строк");
            break;

            // Логические значения
            default : $value = (bool) $value;
        }
    }

    /**
     * Установливает значение параметра настроек.
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     *
     * @throws \Typo\Exception
     *
     * @return void
     */
    public function setOption($name, $value)
    {
        $name = strtolower($name);

        if(!array_key_exists($name, $this->default_options))
            return self::throwException(self::E_OPTION_NAME, "Несуществующий параметр '$name'");

        $this->validateOption($name, $value);

        $this->options[$name] = $value;
        if(method_exists($this, 'onChangeOption'))
            $this->onChangeOption($name, $value);
    }

    /**
     * Установливает значения параметров настроек.
     *
     * @param array $options    Ассоциативный массив ('название параметра' => 'значение').
     *
     * @uses \Typo\Module::setOption()
     *
     * @throws \Typo\Exception
     *
     * @return void
     */
    public function setOptions(array $options)
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

        if(isset($exception)) throw $exception;
    }

    /**
     * Установливает значения параметров настроек по умолчанию.
     *
     * @uses \Typo\Module::setOptions()
     *
     * @return void
     */
    public function setDefaultOptions()
    {
        $this->setOptions($this->default_options);
    }

    /**
     * Возвращает значение параметра.
     *
     * @param string $name  Название параметра.
     *
     * @throws \Typo\Exception
     *
     * @return mixed
     */
    public function getOption($name)
    {
        if(!$this->checkOptionExists($name))
            return self::throwException(self::E_OPTION_NAME, "Несуществующий параметр '$name'");

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
        return $class::$order[$this->stage];
    }

    /**
     * Добавляет модуль.
     *
     * @param string $module    Название модуля.
     * @param array $options    Ассоциативный массив ('название параметра' => 'значение').
     *
     * @throw \Typo\Exception
     *
     * @return void
     */
    public function addModule($module, array $options = array())
    {
        $classname = self::getModuleClassname($module);
        if(!class_exists($classname))
        {
            return self::throwException(self::E_OPTION_VALUE, "Неизвестный модуль '$module' (класс " . $classname . " не найден)");
        }
        elseif(!array_key_exists($classname, $this->modules))
        {
            $typo = ($this instanceof Typo) ? $this : $this->typo;

            $this->modules[$classname] = new $classname($options, $typo);
        }
    }

    /**
     * Устанавливает стадию работы, сортирует используемые модули по приоритетам выполнения.
     *
     * @param string $stage Стадия работы.
     *
     * @return \Typo\Module
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        foreach($this->modules as $module)
        {
            $module->setStage($stage);
        }

        uasort($this->modules, function(Module $a, Module $b) {
            return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
        });

        return $this;
    }

    /**
     * Запускает выполнение текущей стадии.
     *
     * @uses \Typo\Module::getStageMethod()
     * @uses \Typo\Module::getOrder();
     *
     * @return void
     */
    public function executeStage()
    {
        $method = $this->getStageMethod();

        $fl = true;
        foreach($this->modules as $module)
        {
            if($fl && $module->getOrder() >= $this->getOrder())
            {
                if(method_exists($this, $method))
                {
                    $this->$method();
                    # echo get_called_class() . '::' . $method . '<br>';
                }
                $fl = false;
            }
            $module->executeStage();
        }

        if($fl && method_exists($this, $method))
        {
            $this->$method();
            # echo get_called_class() . '::' . $method . '<br>';
        }
    }


    // --- Защищённые методы класса ---

    /**
     * Применяет правила к тексту.
     *
     * @param array $rules      Набор правил.
     * @param array $helpers    Вспомогательные элементы регулярных выражений.
     *
     * @return void
     */
    protected function applyRules(array $rules, array $helpers = array())
    {
        $patterns = array();
        $replaces = array();
        foreach($rules as $key => $value)
        {
            if(is_array($value))
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
                $this->text->preg_replace_callback($patterns[$i], $replaces[$i]);
            else
                $this->text->preg_replace($patterns[$i], $replaces[$i]);
        }
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
     * Вовзвращает массив имён всех стадий.
     *
     * @return array
     */
    protected function getStages()
    {
        return array('A', 'B', 'C', 'D', 'E', 'F');
    }

    /**
     * Вовзращает имя метода текущей стадии.
     *
     * @return string
     */
    protected function getStageMethod()
    {
        return 'stage' . $this->stage;
    }


    // --- Статические методы класса ---

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
            '{a}' => '[\wа-яА-ЯёЁ]',

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
     * @param string $module
     *
     * @return string
     */
    static protected function getModuleClassname($module)
    {
        $classname = __CLASS__;
        foreach(explode('/', $module) as $part)
        {
            if($part === '.')
                $classname = get_called_class();
            else
                $classname .= '\\' . ucfirst($part);
        }
        return $classname;
    }

    /**
     * Выбрасывает исключение.
     *
     * @param int             $code       Код состояния.
     * @param string          $message    Сообщение.
     * @param \Typo\Exception $previous   Предыдущее исключение.
     *
     * @throws \Typo\Exception
     *
     * @return void
     */
    static protected function throwException($code = Exception::E_UNKNOWN, $message = null, Exception $previous = null)
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