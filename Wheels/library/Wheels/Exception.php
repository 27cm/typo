<?php

namespace Wheels;

/**
 * Исключение.
 */
class Exception extends \Exception
{
    /**
     * Предыдущее исключение.
     *
     * @var Exception
     */
    private $_previous = null;


    // --- Коды ошибок ---

    /** Неизвестная ошибка */
    const E_UNKNOWN = 0;

    /** Несуществующий параметр */
    const E_OPTION_NAME = 1;

    /** Недопустимый тип значения параметра */
    const E_OPTION_TYPE = 2;

    /** Недопустимое значение параметра */
    const E_OPTION_VALUE = 3;

    /** Ошибка выполнения. */
    const E_RUNTIME = 4;


    // --- Конструктор ---

    /**
     * @uses parent::__construct()
     *
     * @param string     $message   Сообщение.
     * @param int        $code      Код состояния.
     * @param \Exception $previous  Предыдущее исключение.
     *
     * @return void
     */
    public function __construct($message = '', $code = self::E_UNKNOWN, \Exception $previous = null)
    {
        if(version_compare(PHP_VERSION, '5.3.0', '<'))
        {
            parent::__construct($message, (int) $code);
            $this->_previous = $previous;
        }
        else
        {
            parent::__construct($message, (int) $code, $previous);
            $this->_previous = $previous;
        }
    }


    // --- Открытые методы класса ---

    /**
     * Предоставляет доступа к final методу getPrevious().
     *
     * @param string $method    Имя метода.
     * @param array  $args      Массив аргументов.
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        switch(strtolower($method))
        {
            case 'getprevious' : return $this->_getPrevious();
        }
        return null;
    }

    /**
     * Преобразовывает объект в строку.
     *
     * @return string
     */
    public function __toString()
    {
        if(version_compare(PHP_VERSION, '5.3.0', '<'))
        {
            if($this->hasPrevious())
                return $this->_getPrevious()->__toString() . "\n\nNext " . parent::__toString();
        }
        return parent::__toString();
    }


    // --- Защищенные методы класса ---

    /**
     * Проверяет наличие предыдущего исключения.
     *
     * @return bool
     */
    public function hasPrevious()
    {
        return isset($this->_previous);
    }

    /**
     * Вовзращает предыдущее исключение.
     *
     * @return \Exception
     */
    protected function _getPrevious()
    {
        return $this->_previous;
    }


    // --- Статические методы класса ---

    /**
     * Возвращает стандартное сообщение об ошибке по её коду.
     *
     * @staticvar array $messages   Массив всех сообщений об ошибках.
     *
     * @param int $code Код состояния.
     *
     * @return string
     */
    static public function getMessageByCode($code)
    {
        static $messages = array(
            self::E_UNKNOWN      => 'Неизвестная ошибка',
            self::E_OPTION_NAME  => 'Несуществующий параметр',
            self::E_OPTION_TYPE  => 'Недопустимый тип значения параметра',
            self::E_OPTION_VALUE => 'Недопустимое значение параметра',
            self::E_RUNTIME      => '',
        );

        if(array_key_exists($messages, $code))
            return $messages[$code];
        else
            return $messages[self::E_UNKNOWN];
    }
}