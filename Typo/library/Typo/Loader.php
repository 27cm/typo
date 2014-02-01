<?php

namespace Typo;

/**
 * Загрузчик классов.
 */
class Loader
{
    /**
     * Расширение имён файлов.
     *
     * @var string
     */
    private $fileExt = 'php';

    /**
     * Пространство имён.
     *
     * @var string
     */
    private $namespace = null;

    /**
     * Путь к дирректории с файлами классов.
     * 
     * @var string
     */
    private $includePath = null;

    /**
     * Разделитель частей пространства имён.
     *
     * @var type
     */
    private $sep = '\\';

    /**
     * Создаёт новый <tt>Loader</tt> для загрузки классов указанного пространства имён.
     *
     * @param string $namespace     Пространство имён.
     * @param string $includePath   Путь к дирректории с файлами.
     */
    public function __construct($namespace = null, $includePath = null)
    {
        $this->namespace = $namespace;
        $this->includePath = $includePath;
    }

    /**
     * Регистрирует \Typo\Loader::autoload() в качестве реализации метода __autoload().
     *
     * @param bool $throw   Этот параметр определяет, должна ли spl_autoload_register() выбрасывать исключение,
     *                      если зарегистрировать autoload оказалось невозможным.
     * @param bool $prepend Если передано значение true, spl_autoload_register() поместит
     *                      указанную функцию на дно стэка вместо добавления на вершину.
     *
     * @return bool Возвращает TRUE в случае успешного завершения или FALSE в случае возникновения ошибки.
     */
    public function register($throw = true, $prepend = false)
    {
        return spl_autoload_register(array($this, 'autoload'), $throw, $prepend);
    }

    /**
     * Снимает регистрацию \Typo\Loader::autoload() в качестве реализации метода __autoload().
     *
     * @return bool Возвращает TRUE в случае успешного завершения или FALSE в случае возникновения ошибки.
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'autoload'));
    }

    /**
     * Загружает указанный класс или интерфейс.
     *
     * @param string $classname Имя загружаемого класса или интерфейса.
     *
     * @return void
     */
    public function autoload($classname)
    {
        if (null === $this->namespace
        || $this->namespace.$this->sep === substr($classname, 0, strlen($this->namespace.$this->sep))) {
            $fileName = '';
            $namespace = '';
            if (false !== ($lastNsPos = strripos($classname, $this->sep))) {
                $namespace = substr($classname, 0, $lastNsPos);
                $classname = substr($classname, $lastNsPos + 1);
                $fileName = str_replace($this->sep, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.' . $this->fileExt;

            require ($this->includePath !== null ? $this->includePath . DIRECTORY_SEPARATOR : '') . $fileName;
        }
    }
}
