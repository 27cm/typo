<?php

namespace Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TmixedTest extends AbstractType
{
    static protected $_testValidateData
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
