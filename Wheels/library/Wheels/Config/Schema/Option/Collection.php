<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Schema\Option
 */

namespace Wheels\Config\Schema\Option;

use Wheels\Config\Schema\Option\Exception;

/**
 * Класс обеспечивает доступ к объектам описаний параметров настроек как к массиву.
 */
class Collection extends \Wheels\Datastructure\Collection
{
    // --- Конструктор ---

    public function __construct(array $array = array())
    {
        parent::__construct('Wheels\Config\Schema\Option', $array);
    }


    // --- Открытые методы ---

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if(!is_string($offset))
            throw new Exception('Имя параметра должно быть строкой');

        if( !preg_match('/^[a-zA-Z_\x7F-\xFF][a-zA-Z0-9_\x7F-\xFF]*$/', $offset))
        {
            throw new Exception(
                'Имя параметра должно начинаться с буквы или символа подчеркивания и ' .
                'состоять из букв, цифр и символов подчеркивания'
            );
        }

        parent::offsetSet($offset, $value);
    }
}
