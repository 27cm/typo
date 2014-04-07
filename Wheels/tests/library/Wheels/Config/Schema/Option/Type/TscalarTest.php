<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\AbstractType;

class TscalarTest extends AbstractType
{
    static protected $_testValidateData = array(
        0  => array(23, TRUE),
        1  => array(23.0, TRUE),
        2  => array(1e7, TRUE),
        3  => array(PHP_INT_MAX, TRUE),
        4  => array(PHP_INT_MIN, TRUE),
        5  => array(INF, TRUE),
        6  => array('23', TRUE),
        7  => array('23.0', TRUE),
        8  => array(TRUE, TRUE),
        9  => array(array(), FALSE),
        10 => array(NULL, FALSE),
    );
}
