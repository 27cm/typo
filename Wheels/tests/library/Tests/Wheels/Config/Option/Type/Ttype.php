<?php

namespace Tests\Wheels\Config\Option\Type;

use Wheels\Config\Option\Type;

class Ttype extends Type
{
    public function validate($var)
    {
        return true;
    }
}
