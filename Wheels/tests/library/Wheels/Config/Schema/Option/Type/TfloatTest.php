<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\AbstractType;

class TfloatTest extends AbstractType
{
    static protected $_testValidateData = array(
        array(23.5, TRUE),
        array(1e7, TRUE),
        array(INF, FALSE),
        array('23.5', FALSE),
        array(23, FALSE),
        array(TRUE, FALSE),
        array(NULL, FALSE),
    );
}
