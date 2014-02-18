<?php
$root    = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$include = "{$root}/../../include";
set_include_path(get_include_path() . PATH_SEPARATOR . $include);

require_once "{$root}/../library/Typo.php";

class TypoTest extends PHPUnit_Framework_TestCase {
    protected $typo;

    protected $charsets = array('UTF-8','CP1251','KOI8-R','IBM866','ISO-8859-5','MAC');
    protected $encodings = array('MODE_NONE','MODE_NAMES','MODE_CODES','MODE_HEX_CODES');


    protected function setUp()
    {
        $this->typo = new Typo();
    }

    // Проверяем значения свойств по умолчанию
    public function testDefaultOptions()
    {
        $this->assertEquals($this->typo->getOption('charset'), 'utf-8',  '', 0, 10, false, $ignoreCase = true);
        $this->assertEquals($this->typo->getOption('encoding'), Typo::MODE_NAMES);

        $modules = array('Html', 'Punct', 'Space', 'Dash', 'Nobr', 'Url', 'Quote', 'Math', 'Filepath', 'Smile\Kolobok\Standart');
        array_walk($modules,function(&$item) { $item = 'Typo\Module\\' . $item; });
        $this->assertEquals($this->typo->getOption('modules'), $modules);

        $this->assertEquals($this->typo->getOption('html-in-enabled'), true);
        $this->assertEquals($this->typo->getOption('html-out-enabled'), true);
        $this->assertEquals($this->typo->getOption('html-doctype'), Typo::DOCTYPE_HTML5);
        $this->assertEquals($this->typo->getOption('nl2br'), true);
        $this->assertEquals($this->typo->getOption('e-convert'), false);
    }

    // Установка неизвестного параметра
    public function testSetUnknownParam() {
        try {
            $this->typo->setOption('unknown', 'value');
        }
        catch (Typo\Exception $e) {
            $this->assertStringStartsWith("Несуществующий параметр",$e->getMessage());
        }

    }
    // Установка неправильной кодировки
    public function testSetUnknownCharset() {
        try {
            $this->typo->setOption('charset', 'UNKNOWN');
        }
        catch (Typo\Exception $e) {
            $this->assertStringStartsWith("Неизвестная кодировка",$e->getMessage());
        }
    }

    // Установка существуеющей кодировки
    public function testSetExistingCharset() {
        foreach($this->charsets as $charset) {
            $this->typo->setOption('charset', $charset);
            $this->assertEquals($this->typo->getOption('charset'), $charset,$message = '', $delta = 0, $maxDepth = 10, $canonicalize = false, $ignoreCase = true);
        }
    }

    // Установка неправильного параметра кодировки
    public function testSetUnknownEncoding() {
        try {
            $this->typo->setOption('encoding', 'MODE_UNKNOWN');
        }
        catch (Typo\Exception $e) {
            $this->assertStringStartsWith("Неизвестный режим кодирования спецсимволов",$e->getMessage());
        }
    }

    // Установка существующего параметра кодировки
    public function testSetExistingEncodings() {
        foreach($this->encodings as $encoding) {
            $this->typo->setOption('encoding', $encoding);
            $this->assertEquals($this->typo->getOption('encoding'), $encoding,'', 0, 10, false, $ignoreCase = true);
        }
    }
}