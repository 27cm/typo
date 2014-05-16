<?php

namespace Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TscalarTest extends AbstractType
{
    static protected $_testValidateData
        = array(
            0  => array(23, true),
            1  => array(23.0, true),
            2  => array(1e7, true),
            3  => array(PHP_INT_MAX, true),
            4  => array(PHP_INT_MIN, true),
            5  => array(INF, true),
            6  => array('23', true),
            7  => array('23.0', true),
            8  => array(true, true),
            9  => array(array(), false),
            10 => array(null, false),
        );
}
