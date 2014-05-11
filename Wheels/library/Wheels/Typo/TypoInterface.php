<?php

namespace Wheels\Typo;

interface TypoInterface
{
    /**
     *
     */
    public function setConfigDir($dir);

    /**
     * Устанавливает значения параметров по умолчанию
     * в качестве текущих значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setDefaultOptions();

    /**
     * Устанавливает разрешение изменять параметры.
     *
     * @param bool $value true, если необходимо разрешить добавлять, изменять и удалять
     *                    параметры, и false - в противном случае.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setAllowModifications($value);

    /**
     * Устанавливает значения параметров из заданной группы значений параметров.
     *
     * @param int|float|string|bool $name Название группы значений параметров.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionsFromGroup($name, $required = false);

    /**
     * Устанавливает значения параметров из заданных групп значений параметров.
     *
     * @param array $names Массив названий групп.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setOptionsFromGroups(array $names, $required = false);
}
