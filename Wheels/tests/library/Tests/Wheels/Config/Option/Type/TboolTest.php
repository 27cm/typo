<?php

namespace Tests\Wheels\Config\Option\Type;

use Tests\Wheels\Config\Option\AbstractType;

class TboolTest extends AbstractType
{
    static protected $_dataConvert = array(
        array(1, true),
        array('1', true),
        array('on', true),
        array('ON', true),
        array('true', true),
        array('TRUE', true),
        
        array(0, false),
        array('0', false),
        array('off', false),
        array('OFF', false),
        array('false', false),
        array('FALSE', false),
    );
    
    static protected $_dataValidate = array(
        array(true, true),
        array(false, true),
        array(1, false),
        array('on', false),
        array(null, false),
    );
}
