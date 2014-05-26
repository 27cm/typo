<?php

namespace Tests;

use Iterator;

class JSONTestIterator implements Iterator
{
    private $tests;
    private $cur;

    public function __construct($file)
    {
        $this->tests = array();
        if (!file_exists($file)) {
            return;
        }
        $string = file_get_contents($file);
        $jsonTests = json_decode($string, true);
        foreach ($jsonTests['tests'] as $testGroup) {
            $desc = $testGroup['desc'];
            foreach ($testGroup['group'] as $test) {
                $input = $test['input'];
                $expected = $test['expected'];
                $section = $test['section'] ? : $testGroup['section'] ? : $jsonTests['section'] ? : 'default';

                $this->tests[] = array($input, $expected, $desc, $section);
            }
        }
        $this->cur = 0;
    }

    public function current()
    {
        return $this->tests[$this->cur];
    }

    public function next()
    {
        $this->cur++;
    }

    public function key()
    {
        return $this->cur;
    }

    public function valid()
    {
        return ($this->cur < count($this->tests));
    }

    public function rewind()
    {
        $this->cur = 0;
    }
}