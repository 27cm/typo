<?php

$root    = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..';
$include = "{$root}/include";
set_include_path(get_include_path() . PATH_SEPARATOR . $include);
require_once "{$root}/Typo/library/Typo.php";

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
        $this->typo->addModule(preg_replace("~Test$~","",get_class($this)));
    }

    /**
     * Загрузка XML-файлов из папки resources.
     */
    public function loadXMLFile() {
        $moduleName = preg_replace("~Test$~","",get_class($this));
        $xmlTests = @simplexml_load_file(dirname(__FILE__) . "/resources/{$moduleName}.xml");
        if (!$xmlTests && count($xmlTests->test) == 0) {
            return array(array(0,0,0)); // Говнокод (если не нашлось файла все равно выполнится один тест)
        }
        foreach($xmlTests as $xmlTest) {
            if ($xmlTest->ignore)
                continue;
            $desc = (string)$xmlTest->desc;
            $inputXML = preg_replace('~<\w+>(.+)</\w+>~s','$1',$xmlTest->input->asXML());
            $expectedXML = preg_replace('~<\w+>(.+)</\w+>~s','$1',$xmlTest->expected->asXML());
            $tests[] = array($inputXML,$expectedXML,$desc);
        }

        return $tests;
    }
    /**
     * @dataProvider loadXMLFile
     */
    public function testXMLFiles($input,$expected,$desc) {
        $executed_text = (string)$this->typo->execute($input);
        $executed_text = str_replace("&nbsp;",' ',$executed_text);

        $this->assertEquals($expected,$executed_text,$desc);
    }

}