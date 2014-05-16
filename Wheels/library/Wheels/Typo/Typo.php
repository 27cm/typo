<?php

/**
 * Wheels Library
 *
 * @category Wheels
 * @package  Wheels\Typo
 */

namespace Wheels\Typo;

use Wheels\Typo\Module\Collection as ModulesCollection;
use Wheels\Typo\Module\AbstractModule;
use Wheels\Utility;

/**
 * Типограф.
 *
 * @version 0.1 2014-02-16
 */
class Typo extends AbstractTypo
{
    /**
     * Текст.
     *
     * @var \Wheels\Typo\Text
     */
    protected $_text;

    /**
     * Модули.
     *
     * @var \Wheels\Typo\Module\Collection|\Wheels\Typo\Module\AbstractModule[]
     */
    protected $_modules = array();

    /**
     * Текущая стадия.
     *
     * @var int
     */
    protected $_stageNum;

    /**
     * @var array
     */
    protected $_savedOptions = array();

    /**
     * Коды символов.
     *
     * @link http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references
     *
     * @var array
     */
    static protected $_chars = array(
        'chr' => array(),
        'ord' => array(),
    );

    /**
     * Версия.
     *
     * @var string
     */
    const VERSION = '0.1';


    // --- Заменители ---

    /** Элемент. */
    const REPLACER = 'E';

    /** Видимый элемент. */
    const VISIBLE = '[[[%s%u]]]';

    /** Невидимый элемент. */
    const INVISIBLE = '{{{%s%u}}}';


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = array())
    {
        $this->_modules = new ModulesCollection();
        $this->_text = new Text();

        parent::__construct($options);

        $this->getConfig()->getOption('modules')->addEventListener('setValue', array($this, 'onConfigModulesSet'));

        $this->onConfigModulesSet();
    }

    /**
     * Обрабатывает текст.
     *
     * @param string $text    Исходный текст.
     * @param array  $options Параметры.
     *
     * @return string Обработанный текст.
     */
    public function process($text, array $options = array())
    {
        $this->saveOptions();
        $this->setOptions($options);
        $this->setAllowModifications(false);

        $this->getText()->setText($text, $this->getOption('charset'));

        $charset = $this->getOption('charset');
        $int_encoding = mb_internal_encoding();
        $defaultCharset = 'UTF-8';

        // Изменение кодировки текста на кодировку по умолчанию
        mb_internal_encoding($defaultCharset);
        if ($charset != $defaultCharset) {
            $this->getText()->iconv($charset, $defaultCharset);
        }

        // Выполнение всех стадий
        $stages = array('A', 'B', 'C');
        for ($i = 0; $i < 3; $i++) {
            $stage = $stages[$i];
            $method = 'stage' . $stage;

            $this->getModules()->uasort(function (AbstractModule $a, AbstractModule $b) use ($stage) {
                return ($a->getOrder($stage) < $b->getOrder($stage)) ? -1 : 1;
            });

            foreach ($this->getModules() as $module) {
                if (is_callable(array($module, $method))) {
                    $module->$method();
                }
            }
        }

        // Восстанавление кодировки текста
        if ($charset != $defaultCharset) {
            $this->getText()->iconv($defaultCharset, $charset);
        }
        mb_internal_encoding($int_encoding);

        $this->setAllowModifications(true);
        $this->restoreOptions();

        return $this->getText()->getText();
    }

    /**
     * Возвращает текст.
     *
     * @return \Wheels\Typo\Text Текст.
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Возвращает модули.
     *
     * @return \Wheels\Typo\Module\Collection|\Wheels\Typo\Module\AbstractModule[] Коллекция модулей.
     */
    public function getModules()
    {
        return $this->_modules;
    }

    /**
     * Возвращает модуль с заданным именем.
     *
     * @param string $name Название модуля.
     *
     * @return \Wheels\Typo\Module\AbstractModule Модуль с заданным именем.
     *
     * @throws \Wheels\Typo\Exception
     */
    public function getModule($name)
    {
        if (!$this->hasModule($name)) {
            throw new Exception("Неизвестный модуль '{$name}'");
        }

        return $this->_modules[$name];
    }

    /**
     * Проверяет, что модуль с заданным именем имеется в списке используемых модулей.
     *
     * @param string $name Название модуля.
     *
     * @return bool Возвращает true, если модуль с заданным именем имеется в списке
     *              используемых модулей, и false - в противном случае.
     */
    public function hasModule($name)
    {
        return $this->getModules()->offsetExists($name);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();
        $this->getModules()->setDefaultOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroup($name, $required = false)
    {
        parent::setOptionsFromGroup($name, $required);
        $this->getModules()->setOptionsFromGroup($name, $required);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroups(array $names, $required = false)
    {
        parent::setOptionsFromGroups($names, $required);
        $this->getModules()->setOptionsFromGroups($names, $required);
    }

    /**
     * {@inheritDoc}
     */
    public function setConfigDir($dir)
    {
        parent::setConfigDir($dir);
        $this->getModules()->setConfigDir($dir);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        $options = parent::getOptions();

        $keys = array();
        $modulesOptions = $this->getModules()->getOptions();
        foreach (array_keys($modulesOptions) as $name) {
            $keys[] = 'module.' . $name;
        }
        $modulesOptions = array_values($modulesOptions);
        $modulesOptions = array_combine($keys, $modulesOptions);

        return array_merge($options, $modulesOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions(array $options)
    {
        // @todo: Написать наследника Config
        $o = $this->getConfig()->getOptions();

        foreach ($options as $name => $value) {
            if ($o->prepareOffset($name) === $o->prepareOffset('modules')) {
                $this->setOption($name, $value);
                unset($options[$name]);
                break;
            }
        }

        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setOption($name, $value)
    {
        if (preg_match('~^module\.([^\.]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            $this->getModule($moduleName)->setOptions($value);
        } elseif (preg_match('~^(?:module\.)?([^\.]+)\.([^\.]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            $optionName = $matches[2];
            $this->getModule($moduleName)->setOption($optionName, $value);
        } else {
            parent::setOption($name, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOption($name)
    {
        if (preg_match('~^module\.([^\.]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            return $this->getModule($moduleName)->getOptions();
        } elseif (preg_match('~^(?:module\.)?([^\.]+)\.([^\.]+)$~i', $name, $matches)) {
            $moduleName = $matches[1];
            $optionName = $matches[2];
            return $this->getModule($moduleName)->getOption($optionName);
        } else {
            return parent::getOption($name);
        }
    }

    /**
     * Сохраняет настройки.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function saveOptions()
    {
        $this->_savedOptions = $this->getOptions();
    }

    /**
     * Восстанавливает настройки.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function restoreOptions()
    {
        $this->setOptions($this->_savedOptions);
    }

    /**
     * {@inheritDoc}
     */
    public function setAllowModifications($value)
    {
        $this->getModules()->setAllowModifications($value);
        parent::setAllowModifications($value);
    }

    /**
     * Возвращает номер версии.
     *
     * @return string Номер версии.
     */
    static public function getVersion()
    {
        return static::VERSION;
    }

    /**
     * Обрабатывает заданный текст.
     *
     * @param string $text    Исходный текст.
     * @param array  $options Ассоциативный массив настроек.
     *
     * @return string Обработанный текст.
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


    // --- Защищённые методы ---

    /**
     * Возвращает символ по его коду.
     *
     * @param int $c Код символа.
     *
     * @return string|bool
     */
    protected function chr($c)
    {
//        switch ($this->getOption('encoding')) {
//            case self::MODE_NONE :
//                return Utility::chr($c);
//                break;
//            case self::MODE_CODES :
//                return sprintf('&#%u;', $c);
//                break;
//            default :
//                return sprintf('&#x%x;', $c);
//        }
    }

    /**
     * Обработчик события изменения значения параметра 'modules'.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function onConfigModulesSet()
    {
        $classnames = $this->getOption('modules');
        $modules = array_keys($this->getModules()->getArray());

        foreach ($modules as $classname) {
            if (!in_array($classname, $classnames, true)) {
                unset($this->_modules[$classname]);
            }
        }

        foreach ($classnames as $classname) {
            if (!$this->_hasModule($classname)) {
                /** @var \Wheels\Typo\Module\AbstractModule $module */
                $module = new $classname($this);
                $this->_addModule($module);
            }
        }
    }

    /**
     * Устанавливает модули, указанные в параметре 'modules'.
     */
    protected function _setModules()
    {

    }

    protected function _addModule(AbstractModule $module)
    {
        $this->_modules[] = $module;
    }

    protected function _hasModule($name)
    {
        return $this->getModules()->offsetExists($name);
    }
}
