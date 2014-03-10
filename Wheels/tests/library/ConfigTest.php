<?php

use Wheels\Typo\Config;
use Wheels\Typo\Exception;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * Директория с тестовыми INI файлами.
     *
     * @var string
     */
    protected $config_dir;

    protected function setUp()
    {
        $this->config_dir = TEST_DIR . DS . 'config' . DS . get_called_class();

        $filename = $this->config_dir . DS . 'config.ini';
        chmod($filename, 0777);
    }

    /**
     * Несуществующий конфигурационный файл.
     */
    public function testUnknownFilename()
    {
        $filename = $this->config_dir . DS . 'unknown.ini';
        $this->setExpectedException('Wheels\Typo\Exception', "Файл '$filename' не найден");
        new Config($filename);
    }

    /**
     * Установка неизвестного параметра.
     */
    public function testNotReadableFile()
    {
        $filename = $this->config_dir . DS . 'config.ini';

        // Закрываем файл для чтения
        $mode = fileperms($filename);
        chmod($filename, 0333);

        $this->setExpectedException('Wheels\Typo\Exception', "Файл '$filename' закрыт для чтения");
        new Config($filename);

        // Восстанавливаем права доступа к файлу
        chmod($filename, $mode);
    }

    /**
     * Чтение конфигурационного INI файла.
     */
    public function testProcess()
    {
        $filename = $this->config_dir . DS . 'config.ini';

        $config = new Config($filename);
        $sections = PHPUnit_Framework_Assert::readAttribute($config, 'sections');

        $expected = array(
            'sectionA' => array(
                'simple' => 'simpleA',
                'string' => 'stringA',
                'param' => array(
                    'string' => 'stringA',
                    'bool' => array(
                        'enabled'  => true,
                        'disabled' => false,
                    ),
                ),
                'array' => array('A1', 'A2', 'A3'),
            ),
            'sectionB' => array(
                'simple' => 'simpleA',
                'string' => 'stringB',
                'param' => array(
                    'string' => 'stringB',
                    'bool' => array(
                        'enabled'  => false,
                        'disabled' => false,
                    ),
                ),
                'array' => array('A1', 'A2', 'A3', 'B1', 'B2'),
            ),
            'sectionC' => array(
                'simple' => 'simpleC',
                'param' => array(
                    'string' => 'stringC',
                ),
            ),
        );

        $this->assertEquals($expected, $sections);
    }
}
