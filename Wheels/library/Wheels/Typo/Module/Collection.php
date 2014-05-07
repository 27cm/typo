<?php

/**
 * Wheels Library
 *
 * @category   Wheels
 * @package    Wheels\Config
 * @subpackage Wheels\Config\Option
 */

namespace Wheels\Typo\Module;

use Wheels\Typo\Module;
use Wheels\Typo\IOptions;

/**
 * Коллекция модулей типографа.
 */
class Collection extends \Wheels\Datastructure\Collection implements IOptions
{
    /**
     * Массив модулей.
     *
     * @var \Wheels\Typo\Module
     */
    protected $_array;


    // --- Открытые методы ---

    /**
     * Создаёт коллекцию параметров.
     *
     * @param \Wheels\Typo\Module[] $array Массив модулей.
     */
    public function __construct(array $array = array())
    {
        parent::__construct('Wheels\Typo\Module', $array);
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroup($name)
    {
        return $this->__call(__METHOD__, func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function setOptionsFromGroups(array $names)
    {
        return $this->__call(__METHOD__, func_get_args());
    }
}
