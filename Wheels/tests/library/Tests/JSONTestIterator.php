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

        $data = json_decode($string, true);
        $this->process($data);

        $this->cur = 0;
    }

    public function process(array $data, $_desc = NULL, $_options = NULL)
    {
        $desc = array_key_exists('desc', $data) ? $data['desc'] : $_desc;
        $options = array_key_exists('options', $data) ? $data['options'] : $_options;

        if (!(is_null($options) || is_array($options))) {
            $options = array($options);
        }

        if (array_key_exists('input', $data) && array_key_exists('expected', $data)) {
            $this->tests[] = array($data['input'], $data['expected'], $desc, $options);
        }

        if (array_key_exists('tests', $data)) {
            foreach ($data['tests'] as $tests) {
                $this->process($tests, $desc, $options);
            }
        }
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