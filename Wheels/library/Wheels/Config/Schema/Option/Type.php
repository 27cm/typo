<?php

namespace Wheels\Config\Schema\Option;

use Wheels\Typo\Module;
use Wheels\Typo\Exception;

/**
 * Тип параметра настроек.
 */
abstract class Type
{
    /**
     *
     * @var
     */
    protected $_isArray;

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
        if(!class_exists($classname))
        {
            $alt_classname = __CLASS__ . '\\' . ucfirst(strtolower($classname));
            if(class_exists($alt_classname))
                $classname = $alt_classname;
            else
                Module::throwException(Exception::E_RUNTIME, "Тип (класс) $classname не найден");
        }

        if(is_subclass_of($classname, __CLASS__))
            return new $classname();
        else
            Module::throwException(Exception::E_RUNTIME, "Класс $classname не является наследником класса " . __CLASS__);
    }


    // --- Абстрактные методы ---

    /**
     * Приводит заданного значения к данному типу.
     *
     * @param mixed $value Преобразуемое значение.
     *
     * @return mixed Преобразованное значение.
     */
    abstract public function convert($value);

    /**
     * Проверяет, соответствует ли заданное значение данному типу.
     *
     * @param mixed $value Проверяемое значение.
     *
     * @return bool
     */
    abstract public function validate($value);
}
