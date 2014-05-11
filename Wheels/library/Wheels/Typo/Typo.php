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
 * <code>
 * $typo = new Typo();
 * echo $typo->process('Какой-то текст...');
 * </code>
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
    protected $_stage;

    /**
     * @var array
     */
    protected $_optionsGroups = array();

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


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = array())
    {
        $this->_modules = new ModulesCollection();
        $this->_text = new Text();

        parent::__construct($options);

        // @todo: исправить необходимость принудительного вызова обработчика события
        $this->onConfigOptionsOffsetSet('modules', $this->getConfig()->getOption('modules'));
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
     * @throws Exception
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
     * Устанавливает значения параметров по умолчанию.
     */
    public function setDefaultOptions()
    {
        $this->_optionsGroups = array();
        parent::setDefaultOptions();
        $this->getModules()->setDefaultOptions();
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroup($name, $required = false)
    {
        $names = array($name);
        $this->setOptionsFromGroups($names, $required);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroups(array $names, $required = false)
    {
        $this->_optionsGroups = $names;
        $this->getConfig()->setOptionsValuesFromGroups($names);
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
     * Обрабатывает текст.
     *
     * @param string $text    Исходный текст.
     * @param array  $options Параметры.
     *
     * @return string Обработанный текст.
     */
    public function process($text, array $options = array())
    {
        if (!empty($options)) {
            $common = $this->getOptions();
            $this->setOptions($options);
        }

        $this->setAllowModifications(false);

        // @todo: Все модули должны быть только в классе типографа, а сам типограф не должен являться модулем
        // @todo: Модули должны определять порядок выполнения путём проверки условий, а не с помощью номеров

        if ($text instanceof Text) {
            // @todo: нельзя переопределять
            $this->_text = $text;
        } else {
            $this->getText()->setText($text, $this->getOption('charset'));
        }

        // @todo: исправить ошибки повторного вызова
        // text->getCharset();

        $charset = $this->getOption('charset');
        $int_encoding = mb_internal_encoding();
        $default_charset = 'UTF-8';

        // Меняем кодировку текста
        mb_internal_encoding($default_charset);
        if ($charset != $default_charset) {
            $this->getText()->iconv($charset, $default_charset);
        }

        // Выполнение всех стадий
        $this->_resetStage();
        do {
            $this->_processStage();
        } while ($this->_setNextStage());

        // Восстанавливаем кодировку текста
        if ($charset != $default_charset) {
            $this->getText()->iconv($default_charset, $charset);
        }
        mb_internal_encoding($int_encoding);

        $this->setAllowModifications(true);

        if (isset($common)) {
            $this->setOptions($common);
        }

        return $this->getText()->getText();
    }

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
        return self::VERSION;
    }

    /**
     * Устанавливает значения параметров настроек по умолчанию,
     * затем устанавливает заданные значения параметров настроек
     * и типографирует заданный текст.
     *
     * @param string $text    Исходный текст.
     * @param array  $options Ассоциативный массив ('название параметра' => 'значение').
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


    // --- Защищённые методы ---

    /**
     * Сбрасывает текущую стадию.
     *
     *
     */
    protected function _resetStage()
    {
        $this->_setStage(0);
    }

    protected function _setNextStage()
    {
        return $this->_setStage($this->_stage + 1);
    }

    /**
     * Устанавливает стадию работы, сортирует используемые модули по приоритетам выполнения.
     *
     * @param int $stage Стадия работы.
     *
     * @return bool
     */
    protected function _setStage($stage)
    {
        $count = count(self::_getStages());
        if ($stage < 0 || $stage >= $count)
            return false;

        $this->_stage = $stage;

        foreach ($this->_modules as $module)
            $module->setStage($stage);

        uasort(
            $this->_modules, function (AbstractModule $a, AbstractModule $b) {
                return ($a->getOrder() < $b->getOrder()) ? -1 : 1;
            }
        );

        return true;
    }

    /**
     * Вовзращает имя метода текущей стадии.
     *
     * @return string
     */
    protected function _getStageMethod()
    {
        $stages = self::_getStages();
        $name = $stages[$this->_stage];
        return 'stage' . $name;
    }

    /**
     * Возвращает массив имён всех стадий.
     *
     * @return array
     */
    static protected function _getStages()
    {
        return array_keys(AbstractModule::$_order);
    }


    /**
     * Запускает выполнение текущей стадии.
     *
     * @return void
     */
    protected function _processStage()
    {
        $method = $this->_getStageMethod();

        $fl = true;
        foreach ($this->_modules as $module) {
            if ($fl && $module->getOrder() >= $this->getOrder()) {
                if (method_exists($this, $method) && $this->checkTextType()) {
                    $this->$method();
                }
                $fl = false;
            }
            $module->processStage();
        }

        if ($fl && method_exists($this, $method) && $this->checkTextType()) {
            $this->$method();
        }
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
                AbstractModule::onChangeOption($name, $value);
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

    /**
     * {@inheritDoc}
     */
    public function onConfigOptionsOffsetSet($name, $option)
    {
//        var_dump($option);
//        die();
        switch ($name) {
            case 'modules' :
                $classnames = $option->getValue();
                $modules = $this->getModules();

                foreach ($this->getModules() as $classname => $module) {
                    if (!array_key_exists($classname, $classnames)) {
                        unset($this->_modules[$classname]);
                    }
                }

                foreach ($classnames as $classname) {
                    if (!array_key_exists($classname, $modules)) {
                        /** @var \Wheels\Typo\Module $module */
                        $module = new $classname($this);

                        if (!empty($this->_optionsGroups)) {
                            $module->setOptionsFromGroups($this->_optionsGroups);
                        }

                        $this->_modules[] = $module;
                    }
                }
            break;
        }
    }

    /**
     * Устанавливает модули, указанные в параметре 'modules'.
     */
    protected function _setModules()
    {
        $classnames = $this->getOption('modules');

        $modules = $this->getModules();

        foreach ($this->getModules() as $classname => $module) {
            if (!array_key_exists($classname, $classnames)) {
                unset($this->_modules[$classname]);
            }
        }

        foreach ($classnames as $classname) {
            if (!array_key_exists($classname, $modules)) {
                /** @var \Wheels\Typo\Module\AbstractModule $module */
                $module = new $classname($this);

                if (!empty($this->_optionsGroups)) {
                    $module->setOptionsFromGroups($this->_optionsGroups);
                }

                $this->_modules[] = $module;
            }
        }
    }

    protected function _addModule(AbstractModule $module)
    {

    }
}
