<?php

namespace Tests\Wheels\Config\Option\Type;

use Tests\Wheels\Config\Option\AbstractType;

class TarrayTest extends AbstractType
{
    static protected $_dataValidate = array(
        array(array('this', 'is', 'an array'), true),
        array(array(), true),
        array('string', false),
    );
}
