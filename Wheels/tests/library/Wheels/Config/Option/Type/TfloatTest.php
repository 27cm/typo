<?php

namespace Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TfloatTest extends AbstractType
{
    static protected $_testValidateData = array(
        array(23.5, TRUE),
        array(1e7, TRUE),
        array(INF, TRUE),
        array('23.5', FALSE),
        array(23, FALSE),
        array(TRUE, FALSE),
        array(NULL, FALSE),
    );
}
