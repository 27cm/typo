<?php

namespace Wheels\Typo\Module\Emoticon;

use Wheels\Typo\Module\Module;
use Wheels\Typo\Typo;
use Wheels\Utility;

/**
 * Эмотиконы (смайлики).
 *
 * Заменяет эмотиконы на HTML теги изображений.
 *
 * @link http://en.wikipedia.org/wiki/Emoticon
 */
abstract class Emoticon extends Module
{
    /**
     * {@inheritDoc}
     */
    static protected $_order = array(
        'A' => 35,
        'B' => 0,
        'C' => 0,
        'D' => 5,
    );

    /**
     * Смайлики.
     *
     * @var array
     */
    static public $smiles = array();


    // --- Заменители ---

    /** Эмотикон (смайлик). */
    const REPLACER = 'EMOTICON';


    // --- Открытые методы ---

    /**
     * Стадия A.
     *
     * - Заменяет смайлики на [[[EMOTICON1]]], {{{EMOTICON2}}}, ...
     */
    public function stageA()
    {
        static $emoticons = array();

        $class = get_called_class();

        if (!array_key_exists($class, $emoticons)) {
            $emoticons[$class] = array();
            if (!empty(static::$smiles)) {
                foreach (static::$smiles as $key => $group) {
                    foreach ($group['replaces'] as $emoticon) {
                        if (array_key_exists($emoticon, $emoticons[$class])) {
                            throw new Exception("В массиве '{$class}::\$smiles' обнаружен повторящийся эмотикон {$emoticon}");
                        }
                        $emoticons[$class][$emoticon] = $key;
                    }
                }

                uksort($emoticons[$class], array($this, 'strlencmp'));
            }
        }

        $e =& $emoticons[$class];
        if (!empty($e)) {
            $_this = $this;

            $callback = function ($emoticon) use ($_this, $e) {
                $key = $e[$emoticon];
                $smile = static::$smiles[$key];
                $data = array(
                    'id'       => $smile['id'],
                    'name'     => $smile['name'],
                    'title'    => $smile['title'],
                    'emoticon' => $emoticon,
                    'width'    => $_this->getOption('width'),
                    'height'   => $_this->getOption('height'),
                );

                $attrs = $_this->setAttrs($data);
                foreach ($attrs as &$value) {
                    foreach ($data as $key => $val)
                        $value = str_replace('{' . $key . '}', (string) $val, $value);
                }

                $elem = Utility::createElement($_this->getOption('tag'), null, $attrs);
                return $_this->getTypo()->getText()->pushStorage($elem, Emoticon::REPLACER, self::VISIBLE);
            };

            $this->getTypo()->getText()->replace_callback(array_keys($emoticons[$class]), $callback);
        }
    }

    /**
     * Стадия D.
     *
     * - Заменяет [[[EMOTICON1]]], {{{EMOTICON2}}}, ... на HTML теги с изображениями смайликов.
     */
    public function stageD()
    {
        $this->getTypo()->getText()->popStorage(self::REPLACER, self::VISIBLE);
    }

    public function setAttrs($data)
    {
        $attrs = array();
        foreach ($this->getOption('attrs') as $attr) {
            $a_cond = $attr['cond'];
            if (is_callable($a_cond))
                $a_cond = call_user_func($a_cond, $data, $this);

            if ($a_cond) {
                $a_name = $attr['name'];
                if (is_callable($a_name))
                    $a_name = call_user_func($a_name, $data, $this);

                $a_value = $attr['value'];
                if (is_callable($a_value))
                    $a_value = call_user_func($a_value, $data, $this);

                $attrs[$a_name] = $a_value;
            }
        }

        return $attrs;
    }

    static public function strlencmp($a, $b)
    {
        $len_a = mb_strlen($a);
        $len_b = mb_strlen($b);

        return (($len_a != $len_b) ? $len_b - $len_a : 0);
    }
}