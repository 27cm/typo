<?php

namespace Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TarrayTest extends AbstractType
{
    static protected $_testValidateData
        = array(
            array(array('this', 'is', 'an array'), true),
            array(array(), true),
            array('string', false),
        );
}
