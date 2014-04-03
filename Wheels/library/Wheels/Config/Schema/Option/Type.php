<?php

namespace Wheels\Config\Schema\Option;

use Wheels\Config\Schema\Option\Type\Tarray;

use Wheels\Typo\Module;
use Wheels\Config\Schema\Option\Type\Exception;

/**
 * Тип параметра настроек.
 */
abstract class Type
{

    // --- Открытые методы ---

    /**
     * Создаёт объект класса по его сокращённому имени.
     *
     * @example \Wheels\Config\Schema\Option\Type::create('bool');              // return \Wheels\Config\Schema\Option\Type\Bool;
     * @example \Wheels\Config\Schema\Option\Type::create('\My\Type\Example');  // return \My\Type\Example;
     *
     * @param string $classname
     *
     * @return \Wheels\Config\Schema\Option\Type
     */
    static public function create($classname)
    {
        $matches = array();
        if(preg_match('/(.+)\[\]$/', $classname, $matches))
            return new Tarray($matches[1]);

        if(!class_exists($classname))
        {
            $alt_classname = __CLASS__ . '\\T' . strtolower($classname);
            if(class_exists($alt_classname))
                $classname = $alt_classname;
            else
                throw new Exception("Тип (класс) $classname не найден");
        }

        if(is_subclass_of($classname, __CLASS__))
            return new $classname();
        else
            throw new Exception("Класс $classname не является наследником " . __CLASS__);
    }


    // --- Абстрактные методы ---

    /**
     * Проверяет, соответствует ли заданное значение данному типу.
     *
     * @param mixed $var Проверяемое значение.
     *
     * @return bool
     */
    abstract public function validate($var);
}
