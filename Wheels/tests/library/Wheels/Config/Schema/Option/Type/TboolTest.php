<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\AbstractType;

class TboolTest extends AbstractType
{
    static protected $_testValidateData = array(
        array(TRUE, TRUE),
        array(FALSE, TRUE),
        array(1, FALSE),
        array('on', FALSE),
        array(NULL, FALSE),
    );
}
