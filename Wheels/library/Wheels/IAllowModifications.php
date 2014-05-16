<?php

namespace Wheels;

interface IAllowModifications
{
    /**
     * Устанавливает разрешение изменять объект.
     *
     * @param bool $value true, если необходимо разрешить изменять объект, и false - в противном случае.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function setAllowModifications($value);
}
