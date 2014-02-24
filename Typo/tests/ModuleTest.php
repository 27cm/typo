<?php

use Typo\Exception;

/**
 * Абстрактный класс для теста модулей.
 *
 * Конкретные классы тестов каходятся в директории Module, во всех них тестируются загруженные XML-файлы из папки resources через метод testXMLFiles
 */
abstract class ModuleTest extends PHPUnit_Framework_TestCase
{
    protected $typo;

    protected function setUp() {
        $this->typo = new Typo();
        $this->typo->setOption('modules','url');
    }

    /**
     * Загрузка XML-файлов из папки resources.
     */
    public function loadXMLFile() {
        $moduleName = preg_replace("~Test$~","",get_class($this));
        $xmlFile = dirname(__FILE__) . "/resources/{$moduleName}.xml";
        $tests = array();
        if (!file_exists($xmlFile)) {
            return $tests;
        }
        $xmlTests = simplexml_load_file($xmlFile);
        foreach($xmlTests->group as $testGroup) {
            $desc = (string)$testGroup->attributes()['desc'];
            foreach($testGroup->test as $test)
                $tests[] = array((string)$test->input,(string)$test->expected,$desc);
        }

        return $tests;
    }

    public function testXMLFiles() {
        foreach($this->loadXMLFile() as $test) {
            list($input,$expected,$desc) = $test;
            $executed_text = (string)$this->typo->execute($input);
            $executed_text = str_replace("&nbsp;",' ',$executed_text);

            $this->assertEquals($expected,$executed_text,$desc);
        }
    }

}