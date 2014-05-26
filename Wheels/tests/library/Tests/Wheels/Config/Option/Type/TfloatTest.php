<?php

namespace Tests\Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TfloatTest extends AbstractType
{
    static protected $_testValidateData
        = array(
            array(23.5, true),
            array(1e7, true),
            array(INF, true),
            array('23.5', false),
            array(23, false),
            array(true, false),
            array(null, false),
        );
}
