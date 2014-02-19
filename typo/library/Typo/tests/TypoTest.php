<?php
$root    = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$include = "{$root}/../../include";
set_include_path(get_include_path() . PATH_SEPARATOR . $include);

require_once "{$root}/../../Typo.php";
use Typo\Exception;

class TypoTest extends PHPUnit_Framework_TestCase {
    protected $typo;

    public function charsets() {
        return array(
            array('UTF-8'),
            array('CP1251'),
            array('KOI8-R'),
            array('IBM866'),
            array('ISO-8859-5'),
            array('MAC')
        );
    }
    public function encodings() {
        return array(
            array('MODE_NONE'),
            array('MODE_NAMES'),
            array('MODE_CODES'),
            array('MODE_HEX_CODES')
        );
    }

    protected function setUp()
    {
        $this->typo = new Typo();
    }

    // Проверяем значения свойств по умолчанию
    public function testDefaultOptions()
    {
        $this->assertEquals($this->typo->getOption('charset'), 'utf-8',  '', 0, 10, false, true);
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
        $this->setExpectedException('Exception','Несуществующий параметр');
        $this->typo->setOption('unknown', 'value');
    }
    // Установка неправильной кодировки
    public function testSetUnknownCharset() {
        $this->setExpectedException('Exception',"Неизвестная кодировка",Exception::E_OPTION_VALUE);
        $this->typo->setOption('charset', 'UNKNOWN');
    }

    // Установка существуеющей кодировки
    /**
     * @dataProvider charsets
     */
    public function testSetExistingCharset($charset) {
        $this->typo->setOption('charset', $charset);
        $this->assertEquals($this->typo->getOption('charset'), $charset,'', 0, 10, false, true);
    }

    // Установка неправильного параметра кодировки
    public function testSetUnknownEncoding() {
        $this->setExpectedException('Exception','Неизвестный режим кодирования спецсимволов',Exception::E_OPTION_VALUE);
        $this->typo->setOption('encoding', 'MODE_UNKNOWN');
    }

    // Установка существующего параметра кодировки
    /**
     * @dataProvider encodings
     */
    public function testSetExistingEncodings($encoding) {
        $this->typo->setOption('encoding', $encoding);
        $this->assertEquals($this->typo->getOption('encoding'), $encoding,'', 0, 10, false, true);
    }
}