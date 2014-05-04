<?php

namespace Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TintTest extends AbstractType
{
    static protected $_testValidateData = array(
        array(23, TRUE),
        array(PHP_INT_MAX, TRUE),
        array(PHP_INT_MIN, TRUE),
        array(INF, FALSE),
        array('23', FALSE),
        array(23.0, FALSE),
        array(1e7, FALSE),
        array(TRUE, FALSE),
        array(NULL, FALSE),
    );
}
