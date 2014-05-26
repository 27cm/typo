<?php

namespace Tests\Wheels\Config\Option\Type;

use Tests\Wheels\Config\Option\AbstractType;

class TfloatTest extends AbstractType
{
    static protected $_dataValidate
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
