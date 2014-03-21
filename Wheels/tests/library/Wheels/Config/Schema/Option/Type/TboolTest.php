<?php

namespace Wheels\Config\Schema\Option\Type;

use Wheels\Config\Schema\Option\TypeTest;

class TboolTest extends TypeTest
{
    static protected $_testValidateData = array(
        array(TRUE, TRUE),
        array(FALSE, TRUE),
        array(1, FALSE),
        array('on', FALSE),
        array(NULL, FALSE),
    );

    static protected $_testArrayValidateData = array(
        array(array(TRUE, FALSE, TRUE), TRUE),
        array(array(FALSE), TRUE),
        array(array(TRUE, FALSE, 1, TRUE), FALSE),
        array(array('off', NULL, 1, 0.0), FALSE),
    );
}
