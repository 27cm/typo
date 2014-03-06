<?php

use Wheels\Typo\Exception;


class TypoTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Типограф.
     * 
     * @var \Typo
     */
    protected $typo;

    protected function setUp()
    {
        $this->typo = new Typo();
    }

    /**
     * Проверяем значения свойств по умолчанию.
     */
    public function testDefaultOptions()
    {
        $this->assertEquals($this->typo->getOption('charset'), 'utf-8',  '', 0, 10, false, true);
        $this->assertEquals($this->typo->getOption('encoding'), Typo::MODE_NONE);
        $this->assertEquals($this->typo->getOption('html-in-enabled'), true);
        $this->assertEquals($this->typo->getOption('html-out-enabled'), true);
        $this->assertEquals($this->typo->getOption('html-doctype'), Typo::DOCTYPE_HTML5);
        $this->assertEquals($this->typo->getOption('nl2br'), true);
        $this->assertEquals($this->typo->getOption('e-convert'), false);
    }

    /**
     * Установка неизвестного параметра.
     */
    public function testSetUnknownOption()
    {
        $this->setExpectedException('Typo\Exception', 'Несуществующий параметр');
        $this->typo->setOption('unknown', 'value');
    }
    
    /**
     * Получение неизвестного параметра.
     */
    public function testGetUnknownOption() 
    {
        $this->setExpectedException('Typo\Exception', 'Несуществующий параметр');
        $this->typo->getOption('unknown');
    }
    
    
    /**
     * Установка неправильной кодировки.
     */
    public function testSetUnknownCharset() 
    {
        $charset = $this->typo->getOption('charset');
        
        $this->setExpectedException('Typo\Exception', 'Неизвестная кодировка', Exception::E_OPTION_VALUE);
        $this->typo->setOption('charset', 'unknown');
        
        $this->assertEquals($this->typo->getOption('charset'), $charset);
    }

    /**
     * Установка существуеющей кодировки.
     * 
     * @dataProvider charsets
     */
    public function testSetExistingCharset($charset) 
    {
        $this->typo->setOption('charset', $charset);
        $this->assertEquals($this->typo->getOption('charset'), $charset, '', 0, 10, false, true);
    }

    /**
     * Установка неправильного параметра кодировки.
     */
    public function testSetUnknownEncoding() 
    {
        $encoding = $this->typo->getOption('encoding');
        
        $this->setExpectedException('Typo\Exception', 'Неизвестный режим кодирования спецсимволов', Exception::E_OPTION_VALUE);
        $this->typo->setOption('encoding', 'MODE_UNKNOWN');
        
        $this->assertEquals($this->typo->getOption('encoding'), $encoding);
    }

    /**
     * Установка существующего параметра кодировки.
     * 
     * @dataProvider encodings
     */
    public function testSetExistingEncodings($encoding) 
    {
        $this->typo->setOption('encoding', $encoding);
        $this->assertEquals($this->typo->getOption('encoding'), $encoding, '', 0, 10, false, true);
    }
    
    /**
     * Кодировки.
     */
    public function charsets() 
    {
        return array(
            array('UTF-8'),
            array('CP1251'),
            array('KOI8-R'),
            array('IBM866'),
            array('ISO-8859-5'),
            array('MAC')
        );
    }
    
    /**
     * Режими кодирования спецсимволов.
     */
    public function encodings() 
    {
        return array(
            array(Typo::MODE_NONE),
            array(Typo::MODE_NAMES),
            array(Typo::MODE_CODES),
            array(Typo::MODE_HEX_CODES)
        );
    }
}
