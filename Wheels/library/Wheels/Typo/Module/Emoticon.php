<?php

namespace Wheels\Typo\Module;

use Wheels\Typo;
use Wheels\Typo\Module;
use Wheels\Typo\Utility;
use Wheels\Typo\Exception;

/**
 * Эмотиконы (смайлики).
 *
 * Заменяет эмотиконы на html теги изображений.
 *
 * @link http://en.wikipedia.org/wiki/Emoticon
 */
abstract class Emoticon extends Module
{
    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 35,
        'B' => 0,
        'C' => 0,
        'D' => 5,
        'E' => 0,
        'F' => 0,
    );

    /**
     * Смайлики.
     *
     * @var array
     */
    static public $smiles = array();

    /**
     * Массив номеров эмотиконов.
     *
     * @var int[]
     */
    static protected $emoticons = null;


    // --- Заменитель ---

    const REPLACER = 'EMOTICON';


    // --- Открытые методы класса ---

    public function validateOption($name, &$value)
    {
        switch($name)
        {
            case 'tag' :

            break;

            case 'width' :
            case 'height' :
                if(!is_int($value))
                    $value = intval($value);
            break;

            case 'attrs' :
                if(!is_array($value))
                    return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом, а не " . gettype($value));

                foreach($value as $key => &$attr)
                {
                    if(!is_array($attr) || !array_key_exists('value', $attr))
                        return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом элементов array('value' => '...', ['name' => '...', 'cond' => '...'])");
                    if(!array_key_exists('name', $attr))
                        $attr['name'] = (string) $key;
                    if(!array_key_exists('cond', $attr))
                        $attr['cond'] = true;
                }
            break;

            default : Module::validateOption($name, $value);
        }
    }


    // --- Защищенные методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет смайлики на заменитель.
     */
    protected function stageA()
    {
        $class = get_called_class();

        if(is_null($class::$emoticons))
        {
            $class::$emoticons = array();
            if(!empty($class::$smiles))
            {
                foreach($class::$smiles as $key => $group)
                {
                    foreach($group['replaces'] as $emoticon)
                    {
                        // @todo: сделать W_RUNTIME и не прерывать выполнение
                        if(array_key_exists($emoticon, $class::$emoticons))
                            return self::throwException(Exception::E_RUNTIME, "В массиве '$class::\$smiles' обнаружен повторящийся эмотикон {$emoticon}");
                        $class::$emoticons[$emoticon] = $key;
                    }
                }

                uksort($class::$emoticons, array($this, 'strlencmp'));
            }
        }

        if($this->typo->_options['html-out-enabled'] && !empty($class::$emoticons))
        {
            $_this = $this;

            $callback = function($emoticon) use($_this)
            {
                $class = get_called_class();

                $key = $class::$emoticons[$emoticon];
                $smile = $class::$smiles[$key];
                $data = array(
                    'id'       => $smile['id'],
                    'name'     => $smile['name'],
                    'title'    => $smile['title'],
                    'emoticon' => $emoticon,
                    'width'    => $_this->getOption('width'),
                    'height'   => $_this->getOption('height'),
                );

                $attrs = $_this->setAttrs($data);
                foreach($attrs as &$value)
                {
                    foreach($data as $key => $val)
                        $value = str_replace('{' . $key . '}', (string) $val, $value);
                }

                $elem = Utility::createElement($_this->getOption('tag'), null, $attrs);
                return $_this->text->pushStorage($elem, Emoticon::REPLACER, Typo::VISIBLE);
            };

            $this->typo->text->replace_callback(array_keys($class::$emoticons), $callback);
        }
    }

    /**
     * Стадия D.
     */
    protected function stageD()
    {
        $this->text->popStorage(self::REPLACER, Typo::VISIBLE);
    }

    /**
     *
     * @param type $data
     * @param array $attrs
     */
    public function setAttrs($data)
    {
        $attrs = array();
        foreach($this->_options['attrs'] as $attr)
        {
            $a_cond = $attr['cond'];
            if(is_callable($a_cond))
                $a_cond = call_user_func($a_cond, $data, $this);

            if($a_cond)
            {
                $a_name = $attr['name'];
                if(is_callable($a_name))
                    $a_name = call_user_func($a_name, $data, $this);

                $a_value = $attr['value'];
                if(is_callable($a_value))
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