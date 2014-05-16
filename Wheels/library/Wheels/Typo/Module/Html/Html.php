<?php

namespace Wheels\Typo\Module\Html;

use Wheels\Typo\Typo;
use Wheels\Typo\Text;
use Wheels\Typo\Module\AbstractModule;
use Wheels\Typo\Exception;

/**
 * HTML.
 *
 * Заменяет все теги и указанные блоки на безопасные конструкции, а затем восстанавливает их.
 *
 * @link http://wikipedia.org/wiki/HTML
 */
class Html extends AbstractModule
{
    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static protected $_order = array(
        'A' => 10,
        'B' => 0,
        'C' => 0,
        'D' => 30,
        'E' => 0,
        'F' => 0,
    );

    /**
     * Невидимые HTML блоки.
     *
     * @var string|string[]
     */
    static $invisible_blocks = array('<!-- -->', 'comment', 'head', 'script', 'style');

    /**
     * Видимые HTML теги.
     *
     * @var string|string[]
     */
    static $visible_tags = array('img', 'input');


    // --- Заменители ---

    /** Тег. */
    const REPLACER_TAG = 'TAG';

    /** Блок. */
    const REPLACER_BLOCK = 'BLOCK';

    /** Блок <a>...</a>. */
    const REPLACER_BLOCK_A = 'BLOCK_A';


    // --- Открытые методы ---

    /**
     * @see \Typo\Module::validateOption()
     */
    public function validateOption($name, &$value)
    {
        switch ($name) {
            case 'safe-blocks' :
            case 'typo-attrs' :
                if (is_string($value)) {
                    $value = explode(',', $value);
                }

                if (!is_array($value))
                    return self::throwException(
                        Exception::E_OPTION_VALUE,
                        "Значение параметра '$name' должно быть массивом или строкой, а не " . gettype($value)
                    );

                $value = array_map('trim', $value);
                $value = array_map('mb_strtolower', $value);

                $data = array();
                foreach ($value as $val) {
                    if (!is_string($val))
                        return self::throwException(
                            Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом строк"
                        );

                    if (mb_strlen($val))
                        $data[] = $val;
                }
                $value = $data;
                break;

            default :
                AbstractModule::validateOption($name, $value);
        }
    }


    // --- Защищенные методы ---

    /**
     * Стадия A.
     *
     * Заменяет безопасные блоки и теги на соответствующие заменители.
     */
    public function stageA()
    {
        if ($this->_typo->getOption('html-in-enabled')) {
            $safe_blocks_visible = array_diff($this->getOption('safe-blocks'), self::$invisible_blocks);
            $safe_blocks_invisible = array_intersect($this->getOption('safe-blocks'), self::$invisible_blocks);

            $this->removeBlocks($safe_blocks_visible, self::REPLACER_BLOCK, Typo::VISIBLE);
            $this->removeBlocks($safe_blocks_invisible, self::REPLACER_BLOCK, Typo::INVISIBLE);
            $this->removeBlocks(array('a'), self::REPLACER_BLOCK_A, Typo::VISIBLE);

            $this->removeTags(self::$visible_tags, self::REPLACER_TAG, Typo::VISIBLE);
            $this->removeAllTags(self::REPLACER_TAG, Typo::INVISIBLE);
        } else {
            $this->removeBlocks($this->getOption('safe-blocks'), self::REPLACER_BLOCK, Typo::VISIBLE);

            $this->removeAllTags(self::REPLACER_TAG, Typo::VISIBLE);
        }
    }

    /**
     * Стадия D.
     *
     * Восстанавливает теги и безопасные блоки.
     */
    public function stageD()
    {
        if ($this->_typo->getOption('html-in-enabled')) {
            $this->getTypo()->getText()->popStorage(self::REPLACER_TAG, Typo::INVISIBLE);
            $this->getTypo()->getText()->popStorage(self::REPLACER_TAG, Typo::VISIBLE);

            $this->getTypo()->getText()->popStorage(self::REPLACER_BLOCK_A, Typo::VISIBLE);
            $this->getTypo()->getText()->popStorage(self::REPLACER_BLOCK, Typo::INVISIBLE);
            $this->getTypo()->getText()->popStorage(self::REPLACER_BLOCK, Typo::VISIBLE);
        } else {
            $this->getTypo()->getText()->popStorage(self::REPLACER_TAG, Typo::VISIBLE);

            $this->getTypo()->getText()->popStorage(self::REPLACER_BLOCK, Typo::VISIBLE);
        }
    }

    /**
     * Заменяет блоки в тексте.
     *
     * @param array  $blocks   HTML блоки.
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     *
     * @uses \Wheels\Typo\Text::preg_replace_storage()
     */
    protected function removeBlocks(array $blocks, $replacer, $type)
    {
        $patterns = array();
        foreach ($blocks as $tag) {
            switch ($tag) {
                case '<!-- -->' :
                    $open = '<!--';
                    $close = '-->';
                    break;
                default:
                    $tag = preg_quote($tag, '~');
                    $open = '<\h*' . $tag . '(\h[^>]*)?>';
                    $close = '<\/' . $tag . '>';
            }
            $patterns[] = "{$open}.*{$close}";
        }
        $pattern = implode('|', $patterns);

        $this->getTypo()->getText()->preg_replace_storage("~($pattern)~isU", $replacer, $type);
    }

    /**
     * Заменяет теги в тексте.
     *
     * @param array  $tags     HTML теги.
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     *
     * @uses \Wheels\Typo\Text::preg_replace_storage()
     */
    protected function removeTags(array $tags, $replacer, $type)
    {
        $patterns = array();
        foreach ($tags as $tag) {
            $patterns[] = '<\h*' . preg_quote($tag, '~') . '[^>](\h[^>]*)?>';
        }
        $pattern = '~(' . implode('|', $patterns) . ')~isU';

        $this->getTypo()->getText()->preg_replace_storage($pattern, $replacer, $type);
    }

    /**
     * Заменяет все теги в тексте.
     *
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     *
     * @uses \Wheels\Typo\Text::preg_replace_storage()
     */
    public function removeAllTags($replacer, $type)
    {
        $attrs = $this->getOption('typo-attrs');
        if (!empty($attrs)) {
            $_this = $this;
            /*$typo = new Typo(array(
                'html-in-enabled'  => false,
                'html-out-enabled' => false,
            ));*/

            $pattern = '~(?<=\s)(?<name>' . implode('|', array_map('preg_quote', $attrs))
                . ')\=["\'](?<value>[^"\']*)["\']~iu';

            $callback = function ($matches) use ( /*$typo, */
                $_this
            ) {
                $text = new Text($matches['value'], Text::TYPE_HTML_ATTR_VALUE, $_this->getOption('charset'));
                return $matches['name'] . '="' . /*$typo->execute(*/
                $text /*)*/ . '"';
            };

            $callback2 = function ($matches) use ($_this, $pattern, $callback, $replacer, $type) {
                $data = preg_replace_callback($pattern, $callback, $matches[0]);

                return $_this->getTypo()->getText()->pushStorage($data, $replacer, $type);
            };

            $this->getTypo()->getText()->preg_replace_callback('~<[^>]*\w+[^>]*>~s', $callback2);
        } else
            $this->getTypo()->getText()->preg_replace_storage('~<[^>]*\w+[^>]*>~s', $replacer, $type);
    }
}
