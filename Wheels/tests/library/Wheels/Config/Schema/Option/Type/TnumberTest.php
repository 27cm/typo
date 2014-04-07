<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\AbstractType;

class TnumberTest extends AbstractType
{
    static protected $_testValidateData = array(
        array(23, TRUE),
        array(23.0, TRUE),
        array(1e7, TRUE),
        array(PHP_INT_MAX, TRUE),
        array(PHP_INT_MIN, TRUE),
        array(INF, TRUE),
        array('23', FALSE),
        array('23.0', FALSE),
        array(TRUE, FALSE),
        array(NULL, FALSE),
    );
}
