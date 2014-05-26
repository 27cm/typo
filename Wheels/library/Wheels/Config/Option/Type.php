<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Option
 */

namespace Wheels\Config\Option;

use Wheels\Config\Option\Type\Tarray;
use Wheels\Config\Option\Type\Exception;

/**
 * Тип параметра настроек.
 */
abstract class Type
{

    // --- Открытые методы ---

    /**
     * Преобразовывает значение к заданному типу, если это возможно.
     *
     * @param mixed $var Преобразуемое значение.
     *
     * @return mixed Преобразованное значение или исходное, если не удалось преобразовать.
     */
    public function convert($var)
    {
        return $var;
    }

    /**
     * Создаёт объект класса по имени типа.
     *
     * @param string $name Имя встроенного типа (bool, int, array), тип массив элементов заданного типа ([], bool[], int[][])
     *                     или имя класса-наследника \Wheels\Config\Option\Type (\My\Type\Class, \My\Type\Class[]).
     *
     * @return \Wheels\Config\Option\Type Объект требуемого типа.
     *
     * @throws \Wheels\Config\Option\Type\Exception
     */
    static public function create($name)
    {
        $matches = array();
        if (preg_match('/(.+)\[\]$/', $name, $matches)) {
            return new Tarray($matches[1]);
        }

        if (!class_exists($name)) {
            $alt_classname = __CLASS__ . '\\T' . strtolower($name);
            if (class_exists($alt_classname)) {
                $name = $alt_classname;
            } else {
                throw new Exception("Тип (класс) {$name} не найден");
            }
        }

        if (is_subclass_of($name, __CLASS__)) {
            return new $name();
        } else {
            throw new Exception("Класс {$name} не является наследником " . __CLASS__);
        }
    }


    // --- Абстрактные методы ---

    /**
     * Проверяет, соответствует ли заданное значение данному типу.
     *
     * @param mixed $var Проверяемое значение.
     *
     * @return bool Возвращает TRUE, если var значение соответствует данному типа,
     *              в противном случае метод возвращает FALSE.
     */
    abstract public function validate($var);
}
