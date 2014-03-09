<?php

class XMLTestIterator implements Iterator{
    private $tests;
    private $cur;
    public function __construct($file) {
        $this->tests = array();
        if (!file_exists($file)) {
            return;
        }
        $xmlTests = simplexml_load_file($file);
        foreach($xmlTests->group as $testGroup) {
            $desc = (string)$testGroup->attributes()['desc'];
            foreach($testGroup->test as $test) {
                $input = (string)$test->input;
                $expected = strip_tags($test->expected->asXml());
                $config = (string)($test->attributes()['config'] ?: $testGroup->attributes()['config'] ?: $xmlTests->attributes()['config']);
                $section = (string)($test->attributes()['section'] ?: $testGroup->attributes()['section'] ?: $xmlTests->attributes()['section']);

                $this->tests[] = array($input, $expected, $desc, $config, $section);
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