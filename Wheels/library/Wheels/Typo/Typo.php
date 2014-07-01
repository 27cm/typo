<?php

/**
 * Wheels Library
 *
 * @category Wheels
 * @package  Wheels\Typo
 */

namespace Wheels\Typo;

use Wheels\Typo\Module\Collection as ModulesCollection;
use Wheels\Typo\Config\Config as TypoConfig;
use Wheels\Typo\Module\Module;
use Wheels\Utility;

/**
 * Типограф.
 *
 * @version 1.0
 */
class Typo extends AbstractTypo
{
    /**
     * Конфигурация.
     *
     * @var \Wheels\Typo\Config\Config
     */
    protected $_config;

    /**
     * Текст.
     *
     * @var \Wheels\Typo\Text
     */
    protected $_text;

    /**
     * Модули.
     *
     * @var \Wheels\Typo\Module\Collection|\Wheels\Typo\Module\Module[]
     */
    protected $_modules = array();

    /**
     * Текущая стадия.
     *
     * @var int
     */
    protected $_stageNum;

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
    const VERSION = '1.0';


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = array())
    {
        $this->_modules = new ModulesCollection();
        $this->_text = new Text();

        $schema = static::_getConfigSchema();
        $this->_config = TypoConfig::create($schema, array($this, array(), false));
        $this->setOptions($options);

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

        $this->getText()->setText($text, 'UTF-8');


        // Изменение кодировки текста на кодировку по умолчанию
        $encoding = mb_internal_encoding();
        mb_internal_encoding('UTF-8');

        // Выполнение всех стадий
        $stages = array('A', 'B', 'C', 'D');
        for ($i = 0, $count = count($stages); $i < $count; $i++) {
            $stage = $stages[$i];
            $method = 'stage' . $stage;

            $this->getModules()->uasort(function (Module $a, Module $b) use ($stage) {
                return ($a->getOrder($stage) < $b->getOrder($stage)) ? -1 : 1;
            });

            foreach ($this->getModules() as $module) {
                if (is_callable(array($module, $method))) {
                    $module->$method();
                }
            }
        }

        // Восстановление кодировки текста
        mb_internal_encoding($encoding);

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
     * @return \Wheels\Typo\Module\Collection|\Wheels\Typo\Module\Module[] Коллекция модулей.
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
     * @return \Wheels\Typo\Module\Module Модуль с заданным именем.
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

    public function addModule(Module $module)
    {
        // @todo: добавить проверку, есть ли указанный модуль в конфиге ????
        $this->_modules[] = $module;
    }

    public function removeModule($name)
    {
        unset($this->_modules[$name]);
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
                $this->removeModule($classname);
            }
        }

        foreach ($classnames as $classname) {
            if (!$this->hasModule($classname)) {
                /** @var \Wheels\Typo\Module\Module $module */
                $module = new $classname($this);
                $this->addModule($module);
            }
        }
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
}
