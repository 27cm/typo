<?php

namespace Wheels\Typo\Module;

use Wheels\Typo;
use Wheels\Typo\Module;
use Wheels\Typo\Utility;

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


    // --- Заменитель ---

    const REPLACER = 'EMOTICON';


    // --- Защищенные методы класса ---

    public function validateOption($name, &$value)
    {
        switch($name)
        {
            case 'url' :

            break;

            case 'attrs' :
                if(!is_array($value))
                    return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом, а не " . gettype($value));

                foreach($value as &$attr)
                {
                    if(!is_array($attr) || !array_key_exists('name', $attr) || !array_key_exists('value', $attr))
                        return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть массивом элементов array('name' => '...', 'value' => '...', ['cond' => '...'])");
                    if(!array_key_exists('cond', $attr))
                        $attr['cond'] = true;
                }
            break;

            default : Module::validateOption($name, $value);
        }
    }

    /**
     * Стадия A.
     *
     * Заменяет смайлики на заменитель.
     *
     * @return void
     */
    protected function stageA()
    {
        if(!$this->typo->options['html-out-enabled'])
            return;

        $class = get_called_class();
        if(empty($class::$smiles))
            return;

        $_this = $this;

        $smiles = array();
        foreach($class::$smiles as $i => $group)
        {
            foreach($group['replaces'] as $replace)
                $smiles[$replace] = $i;
        }

        uksort($smiles, function ($a, $b) {
            return (strlen($a) > strlen($b)) ? -1 : 1;
        });

        $callback = function($smile) use($_this, $smiles, $class)
        {
            $i = $smiles[$smile];
            $parts = $class::$smiles[$i];

            $src = $_this->getOption('url');
            foreach($parts as $key => $value)
            {
                if(!is_array($value))
                    $src = str_replace('{' . $key . '}', (string) $value, $src);
            }
            $parts = array_merge($parts, array('smile' => $smile));

            $attrs = array('src' => $src);

            $_this->setAttrs($parts, $attrs);

            $data = Utility::createElement('img', null, $attrs);
            return $_this->text->pushStorage($data, Emoticon::REPLACER, Typo::VISIBLE);
        };

        $this->typo->text->replace_callback(array_keys($smiles), $callback);
    }

    /**
     * Стадия D.
     *
     * Восстанавливает смайлики.
     */
    protected function stageD()
    {
        if($this->typo->options['html-out-enabled'])
        {
            $this->text->popStorage(self::REPLACER, Typo::VISIBLE);
        }
    }

    /**
     *
     * @param type $parts
     * @param array $attrs
     */
    public function setAttrs($parts, array &$attrs)
    {
        foreach($this->options['attrs'] as $attr)
        {
            $a_cond = $attr['cond'];
            if(is_callable($a_cond))
                $a_cond = call_user_func($a_cond, $parts, $this);

            if($a_cond)
            {
                $a_name = $attr['name'];
                if(is_callable($a_name))
                    $a_name = call_user_func($a_name, $parts, $this);

                $a_value = $attr['value'];
                if(is_callable($a_value))
                    $a_value = call_user_func($a_value, $parts, $this);

                $attrs[$a_name] = $a_value;
            }
        }
    }

    static public function attrTitle(array $parts, Module $_this)
    {
        return htmlspecialchars($parts['smile']);
    }

    static public function attrAlt(array $parts, Module $_this)
    {
        return htmlspecialchars($parts['smile']);
    }
}