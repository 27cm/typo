<?php

namespace Wheels\Typo\Module\Html;

use Wheels\Typo\Text;
use Wheels\Typo\Module\Module;

/**
 * HTML.
 *
 * Предотвращает типографирование HTML тегов в тексте.
 *
 * @link http://wikipedia.org/wiki/HTML
 */
class Html extends Module
{
    /**
     * {@inheritDoc}
     */
    static protected $_order = array(
        'A' => 10,
        'B' => 0,
        'C' => 30,
        'D' => 0,
    );

    /**
     * Невидимые HTML блоки. Остальные HTML
     *
     * @var string|string[]
     */
    static protected $invisibleBlocks = array('<!-- -->', 'comment', 'head', 'script', 'style');

    /**
     * Видимые HTML теги.
     *
     * @var string|string[]
     */
    static $visibleTags = array('img', 'input');


    // --- Заменители ---

    /** Тег. */
    const REPLACER_TAG = 'TAG';

    /** Блок. */
    const REPLACER_BLOCK = 'BLOCK';


    // --- Открытые методы ---

    /**
     * Стадия A.
     *
     * - Заменяет безопасные блоки на [[[BLOCK1]]], {{{BLOCK2}}}, ...
     * - Заменяет теги на [[[TAG1]]], {{{TAG2}}}, ...
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function stageA()
    {
        $safe_blocks = $this->getOption('safe-blocks');

        $safe_blocks_visible = array_diff($safe_blocks, static::$invisibleBlocks);
        $safe_blocks_invisible = array_intersect($safe_blocks, static::$invisibleBlocks);

        $this->removeBlocks($safe_blocks_visible, self::REPLACER_BLOCK, self::VISIBLE);
        $this->removeBlocks($safe_blocks_invisible, self::REPLACER_BLOCK, self::INVISIBLE);

        $this->removeTags(static::$visibleTags, self::REPLACER_TAG, self::VISIBLE);
        $this->removeAllTags(self::REPLACER_TAG, self::INVISIBLE);
    }

    /**
     * Стадия C.
     *
     * - Восстанавливает теги;
     * - Восстанавливает безопасные блоки.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function stageC()
    {
        $this->getTypo()->getText()->popStorage(self::REPLACER_TAG, self::INVISIBLE);
        $this->getTypo()->getText()->popStorage(self::REPLACER_TAG, self::VISIBLE);

        $this->getTypo()->getText()->popStorage(self::REPLACER_BLOCK, self::INVISIBLE);
        $this->getTypo()->getText()->popStorage(self::REPLACER_BLOCK, self::VISIBLE);
    }


    // --- Защищённые методы ---

    /**
     * Заменяет блоки в тексте.
     *
     * @param array  $blocks   Массив названий HTML тегов.
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     *
     * @return void Этот метод не возвращает значения после выполнения.
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

        $this->getTypo()->getText()->preg_replace_storage("~({$pattern})~isU", $replacer, $type);
    }

    /**
     * Заменяет теги в тексте.
     *
     * @param array  $tags     Массив названий HTML тегов.
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    protected function removeTags(array $tags, $replacer, $type)
    {
        $patterns = array();
        foreach ($tags as $tag) {
            $patterns[] = '<\h*' . preg_quote($tag, '~') . '[^>](\h[^>]*)?>';
        }
        $pattern = '~(' . implode('|', $patterns) . ')~isU';

        $this->preg_replace_tags_storage($pattern, $replacer, $type);
    }

    /**
     * Заменяет все теги в тексте.
     *
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    protected function removeAllTags($replacer, $type)
    {
        $this->preg_replace_tags_storage('~<[^>]*\w+[^>]*>~s', $replacer, $type);
    }

    /**
     * @param $pattern
     * @param $replacer
     * @param $type
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    protected function preg_replace_tags_storage($pattern, $replacer, $type)
    {
        $attrs = $this->getOption('typo-attrs');

        if (!empty($attrs)) {
            $_this = $this;

            $pattern2 = '~(?<=\s)(?<name>' . implode('|', array_map('preg_quote', $attrs)) . ')\=["\'](?<value>[^"\']*)["\']~iu';

            $callback = function ($matches) use ( /*$typo, */ $_this) {
                $text = new Text($matches['value'], Text::TYPE_HTML_ATTR_VALUE, 'UTF-8');
                return $matches['name'] . '="' . /*$typo->execute(*/ $text /*)*/ . '"';
            };

            $callback2 = function ($matches) use ($_this, $pattern2, $callback, $replacer, $type) {
                $data = preg_replace_callback($pattern2, $callback, $matches[0]);
                return $_this->getTypo()->getText()->pushStorage($data, $replacer, $type);
            };

            $this->getTypo()->getText()->preg_replace_callback($pattern, $callback2);
        } else {
            $this->getTypo()->getText()->preg_replace_storage($pattern, $replacer, $type);
        }
    }
}
