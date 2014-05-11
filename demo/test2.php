<?php

header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL | E_STRICT);

class A
{
    protected $b;

    protected $param;

    public function __construct($param)
    {
        $this->b = new B;
        $this->param = $param;

        $this->b->setOnChange(array($this, '_onChange'));
    }

    public function _onChange($name)
    {
        echo __METHOD__ . '(' . $name . ')' . $this->param;
        $this->param = 'param2';
    }

    public function change($name)
    {
        $this->b->change($name);
    }

    public function getParam()
    {
        return $this->param;
    }
}

class B
{
    protected $onChange;

    public function setOnChange($callback)
    {
        $this->onChange = $callback;
    }

    public function change($name)
    {
        call_user_func_array($this->onChange, func_get_args());
    }
}

$a = new A('param1');
$a->change('name7');
echo $a->getParam();
