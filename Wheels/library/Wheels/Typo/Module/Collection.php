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
use Wheels\Typo\Module\Exception;

/**
 * Коллекция модулей типографа.
 */
class Collection extends \Wheels\Datastructure\Collection
{

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
}
