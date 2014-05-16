<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Option
 */

namespace Wheels\Config\Option;

interface OptionInterface
{
    /**
     * Возвращает имя параметра.
     *
     * @return string Имя параметра.
     */
    public function getName();

    /**
     * Возвращает значение параметра.
     *
     * @return mixed Значение параметра.
     */
    public function getValue();

    /**
     * Возвращает значение параметра по умолчанию.
     *
     * @return mixed Значение параметра по умолчанию.
     */
    public function getDefault();

    /**
     * Возвращает тип параметра.
     *
     * @return \Wheels\Config\Option\Type Тип параметра.
     */
    public function getType();

    /**
     * Возвращает текстовое описание параметра.
     *
     * @return string|null Текстовое описание параметра или NULL, если описание не было задано.
     */
    public function getDesc();

    /**
     * Возвращает ассоциативный массив псевдонимов.
     *
     * @return array Ассоциативный массив псевдонимов.
     */
    public function getAliases();

    /**
     * Возвращает массив допустимых значений.
     *
     * @return array Массив допустимых значений.
     */
    public function getAllowed();

    /**
     * Задаёт имя параметра.
     *
     * @param string $name Имя параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setName($name);

    /**
     * Задаёт значение параметра.
     *
     * @param mixed $value Значение параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setValue($value);

    /**
     * Устанавливает значение параметра по умолчанию в качестве текущего значения параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setValueDefault();

    /**
     * Задаёт значение параметра по умолчанию.
     *
     * @param mixed $value Значение параметра по умолчанию.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setDefault($value);

    /**
     * Задаёт текстовое описание параметра.
     *
     * @param string $desc Текстовое описание параметра.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setDesc($desc);

    /**
     * Задаёт массив псевдонимов.
     *
     * @param array $aliases Ассоциативный массив псевдонимов.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setAliases(array $aliases);

    /**
     * Задаёт массив допустимых значений.
     *
     * @param array $allowed Массив допустимых значений.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     *
     * @throws \Wheels\Config\Option\Exception
     */
    public function setAllowed(array $allowed);

    /**
     * Проверяет значение параметра.
     *
     * @param mixed $value Значение параметра.
     *
     * @return bool Если значение параметра является допустимым, то метод возвращает TRUE, иначе - FALSE.
     */
    public function validate($value);
}
