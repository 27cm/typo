<?php

namespace Wheels\Config\Option\Type;

use Wheels\Config\Option\AbstractType;

class TmixedTest extends AbstractType
{
    static protected $_testValidateData = array(
        array(23, TRUE),
        array(INF, TRUE),
        array('23', TRUE),
        array(23.0, TRUE),
        array(1e7, TRUE),
        array(TRUE, TRUE),
        array(NULL, TRUE),
    );
}
