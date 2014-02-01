<?php

namespace Typo\Module;

use Typo;
use Typo\Module;

/**
 * HTML.
 *
 * Заменяет все теги и указанные блоки на безопасные конструкции, а затем восстанавливает их.
 *
 * @link http://wikipedia.org/wiki/HTML
 */
class Html extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array(
        /**
         * Безопасные блоки, содержимое которых не обрабатывается типографом.
         *
         * @var string|string[]
         */
        'safe-blocks' => array('<!-- -->', 'code', 'comment', 'pre', 'script', 'style'),
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 15,
        'B' => 0,
        'C' => 20,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );

    /**
     * Невидимые HTML блоки.
     *
     * @var array
     */
    static $invisible_blocks = array('<!-- -->', 'comment', 'head', 'script', 'style');

    /**
     * Видимые HTML теги.
     *
     * @var array
     */
    static $visible_tags = array('img', 'input');


    // --- Заменители ---

    /** Тег. */
    const REPLACER_TAG = 'TAG';

    /** Блок. */
    const REPLACER_BLOCK = 'BLOCK';


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
            // Безопасные блоки
            case 'safe-blocks' :
                if(is_string($value))
                    $value = explode(',', $value);

                if(!is_array($value))
                    return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом или строкой, а не " . gettype($value));

                foreach($value as &$val)
                {
                    if(!is_string($val) || mb_strlen($val) == 0)
                        return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом непустых строк");

                    $val = mb_strtolower($val);
                }
            break;

            default : Module::validateOption($name, $value);
        }
    }

    /**
     * Стадия A.
     *
     * Заменяет безопасные блоки и теги на соответствующие заменители.
     *
     * @return void
     */
    protected function stageA()
    {
        if($this->typo->options['html-in-enabled'])
        {
            $safe_blocks_visible = array_diff($this->options['safe-blocks'], self::$invisible_blocks);
            $safe_blocks_invisible = array_intersect($this->options['safe-blocks'], self::$invisible_blocks);

            $this->text->removeBlocks($safe_blocks_visible, self::REPLACER_BLOCK, Typo::VISIBLE);
            $this->text->removeBlocks($safe_blocks_invisible, self::REPLACER_BLOCK, Typo::INVISIBLE);

            $this->text->removeTags(self::$visible_tags, self::REPLACER_TAG, Typo::VISIBLE);
            $this->text->removeAllTags(self::REPLACER_TAG, Typo::INVISIBLE);
        }
        else
        {
            $this->text->removeBlocks($this->options['safe-blocks'], self::REPLACER_BLOCK, Typo::VISIBLE);

            $this->text->removeAllTags(self::REPLACER_TAG, Typo::VISIBLE);
        }
    }

    /**
     * Стадия C.
     *
     * Восстанавливает теги и безопасные блоки.
     *
     * @return void
     */
    protected function stageC()
    {
        if($this->typo->options['html-in-enabled'])
        {
            $this->text->popStorage(self::REPLACER_TAG, Typo::VISIBLE);
            $this->text->popStorage(self::REPLACER_TAG, Typo::INVISIBLE);

            $this->text->popStorage(self::REPLACER_BLOCK, Typo::VISIBLE);
            $this->text->popStorage(self::REPLACER_BLOCK, Typo::INVISIBLE);
        }
        else
        {
            $this->text->popStorage(self::REPLACER_TAG, Typo::VISIBLE);

            $this->text->popStorage(self::REPLACER_BLOCK, Typo::VISIBLE);
        }
    }
}