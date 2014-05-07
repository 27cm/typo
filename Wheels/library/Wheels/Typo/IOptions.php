<?php

namespace Wheels\Typo;

interface IOptions
{
    public function setOptionsFromGroup($name);

    public function setOptionsFromGroups(array $names);
}
