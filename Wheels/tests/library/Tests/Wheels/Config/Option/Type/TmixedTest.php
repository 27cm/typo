<?php

namespace Tests\Wheels\Config\Option\Type;

use Tests\Wheels\Config\Option\AbstractType;

class TmixedTest extends AbstractType
{
    static protected $_dataValidate
        = array(
            array(23, true),
            array(INF, true),
            array('23', true),
            array(23.0, true),
            array(1e7, true),
            array(true, true),
            array(null, true),
        );
}
