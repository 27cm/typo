<?php

namespace Typo\Module;

use Typo;
use Typo\Text;
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

        /**
         * Атрибуты тегов, подлежащие типографированию.
         *
         * var string|string[]
         */
        'typo-attrs' => array('title', 'alt'),
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 5,
        'B' => 0,
        'C' => 30,
        'D' => 0,
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


    // --- Открытые методы класса ---

    /**
     * Проверка значения параметра (с возможной корректировкой).
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     */
    public function validateOption($name, &$value)
    {
        switch($name)
        {
            case 'safe-blocks' :
            case 'typo-attrs' :
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


    // --- Защищенные методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет безопасные блоки и теги на соответствующие заменители.
     */
    protected function stageA()
    {
        if($this->typo->options['html-in-enabled'])
        {
            $safe_blocks_visible = array_diff($this->options['safe-blocks'], self::$invisible_blocks);
            $safe_blocks_invisible = array_intersect($this->options['safe-blocks'], self::$invisible_blocks);

            $this->removeBlocks($safe_blocks_visible, self::REPLACER_BLOCK, Typo::VISIBLE);
            $this->removeBlocks($safe_blocks_invisible, self::REPLACER_BLOCK, Typo::INVISIBLE);
            $this->removeBlocks(array('a'), self::REPLACER_BLOCK_A, Typo::VISIBLE);

            $this->removeTags(self::$visible_tags, self::REPLACER_TAG, Typo::VISIBLE);
            $this->removeAllTags(self::REPLACER_TAG, Typo::INVISIBLE);
        }
        else
        {
            $this->removeBlocks($this->options['safe-blocks'], self::REPLACER_BLOCK, Typo::VISIBLE);

            $this->removeAllTags(self::REPLACER_TAG, Typo::VISIBLE);
        }
    }

    /**
     * Стадия C.
     *
     * Восстанавливает теги и безопасные блоки.
     */
    protected function stageC()
    {
        if($this->typo->options['html-in-enabled'])
        {
            $this->text->popStorage(self::REPLACER_TAG, Typo::INVISIBLE);
            $this->text->popStorage(self::REPLACER_TAG, Typo::VISIBLE);

            $this->text->popStorage(self::REPLACER_BLOCK_A, Typo::VISIBLE);
            $this->text->popStorage(self::REPLACER_BLOCK, Typo::INVISIBLE);
            $this->text->popStorage(self::REPLACER_BLOCK, Typo::VISIBLE);
        }
        else
        {
            $this->text->popStorage(self::REPLACER_TAG, Typo::VISIBLE);

            $this->text->popStorage(self::REPLACER_BLOCK, Typo::VISIBLE);
        }
    }

    /**
     * Заменяет блоки в тексте.
     *
     * @param array  $blocks    HTML блоки.
     * @param string $replacer  Имя строки для замены.
     * @param string $type      Тип заменителя.
     *
     * @uses \Typo\Text::preg_replace_storage()
     */
    protected function removeBlocks(array $blocks, $replacer, $type)
    {
        $patterns = array();
        foreach ($blocks as $tag)
        {
            switch($tag)
            {
                case '<!-- -->' :
                    $open = '<!--';
                    $close = '-->';
                break;
                default:
                    $open = "<\h*{$tag}(\h[^>]*)?>";
                    $close = "<\/{$tag}>";
            }
            $patterns[] = "$open.*$close";
        }
        $pattern = implode('|', $patterns);

        $this->text->preg_replace_storage("~($pattern)~isU", $replacer, $type);
    }

    /**
     * Заменяет теги в тексте.
     *
     * @param array  $tags      HTML теги.
     * @param string $replacer  Имя строки для замены.
     * @param string $type      Тип заменителя.
     *
     * @uses \Typo\Text::preg_replace_storage()
     */
    protected function removeTags(array $tags, $replacer, $type)
    {
        $patterns = array();
        foreach ($tags as $tag)
        {
            $patterns[] = "<\h*{$tag}[^>](\h[^>]*)?>";
        }
        $pattern = implode('|', $patterns);

        $this->text->preg_replace_storage("~($pattern)~isU", $replacer, $type);
    }

    /**
     * Заменяет все теги в тексте.
     *
     * @param string $replacer  Имя строки для замены.
     * @param string $type      Тип заменителя.
     *
     * @uses \Typo\Text::preg_replace_storage()
     */
    public function removeAllTags($replacer, $type)
    {
        if(!empty($this->options['typo-attrs']))
        {
            $_this = $this;
            $typo = new Typo(array(
                'html-in-enabled'  => false,
                'html-out-enabled' => false,
            ));

            $pattern = '~(?<=\s)(?<name>' . implode('|', array_map('preg_quote', $this->options['typo-attrs'])) . ')\=["\'](?<value>[^"\']*)["\']~iu';

            $callback = function($matches) use($typo, $_this) {
                $text = new Text($matches['value'], Text::TYPE_HTML_ATTR_VALUE, $_this->options['charset']);
                return $matches['name'] . '="' . $typo->execute($text) . '"';
            };

            $callback2 = function($matches) use($_this, $pattern, $callback, $replacer, $type)
            {
                $data = preg_replace_callback($pattern, $callback, $matches[0]);

                return $_this->text->pushStorage($data, $replacer, $type);
            };

            $this->text->preg_replace_callback('~<[^>]*>~s', $callback2);
        }
        else
            $this->text->preg_replace_storage('~<[^>]*>~s', $replacer, $type);
    }
}