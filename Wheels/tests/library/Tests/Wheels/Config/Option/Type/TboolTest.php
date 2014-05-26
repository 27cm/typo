<?php

namespace Tests\Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TboolTest extends AbstractType
{
    static protected $_testValidateData
        = array(
            array(true, true),
            array(false, true),
            array(1, false),
            array('on', false),
            array(null, false),
        );
}
