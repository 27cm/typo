<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\AbstractType;

class TarrayTest extends AbstractType
{
    static protected $_testValidateData = array(
        array(array('this', 'is', 'an array'), TRUE),
        array(array(), TRUE),
        array('string', FALSE),
    );
}
