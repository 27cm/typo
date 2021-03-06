<?php

namespace Tests\Wheels\Config\Option\Type;

use Tests\Wheels\Config\Option\AbstractType;

class TnumberTest extends AbstractType
{
    static protected $_dataValidate
        = array(
            array(23, true),
            array(23.0, true),
            array(1e7, true),
            array(PHP_INT_MAX, true),
            array(PHP_INT_MIN, true),
            array(INF, true),
            array('23', false),
            array('23.0', false),
            array(true, false),
            array(null, false),
        );
}
