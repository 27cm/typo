<?php
require_once 'XMLTestIterator.php';
use Wheels\Typo;

/**
 * Абстрактный класс для теста модулей.
 *
 * Конкретные классы тестов находятся в директории Module, во всех них тестируются загруженные XML-файлы из папки resources через метод testXMLFiles
 */
abstract class ConcreteModuleTest extends PHPUnit_Framework_TestCase
{
    protected $typo;

    protected function setUp() {
        $this->typo = new Typo();
        $this->typo->setOption('modules',preg_replace("~Test$~","",get_class($this)));
    }

    public function XMLProvider() {
        $moduleName = preg_replace("~Test$~","",get_class($this)) . '.xml';
        return new XMLTestIterator("resources/{$moduleName}");
    }
    /**
     * @dataProvider XMLProvider
     */
    public function testXMLFiles($input,$expected,$desc,$config,$section) {
        //$this->typo->setOptionsFromFile($config,$section);
        $executed_text = (string)$this->typo->process($input);
        $this->assertEquals($expected,$executed_text,$desc);
    }

}