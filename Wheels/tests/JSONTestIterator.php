<?php

class JSONTestIterator implements Iterator{
    private $tests;
    private $cur;
    public function __construct($file) {
        // ...
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