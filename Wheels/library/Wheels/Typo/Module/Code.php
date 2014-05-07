<?php

namespace Wheels\Typo\Module;

use Wheels\Typo;
use Wheels\Typo\Module;
use Wheels\Typo\Utility;
use Wheels\Typo\Exception;

/**
 * Программный код.
 *
 * Использует тег &lt;code&gt; для отображения одной или нескольких строк текста, который представляет собой программный код.
 * Сюда относятся имена переменных, ключевые слова, тексты функции и т.д.
 */
class Code extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    static protected $_default_options
        = array(
            /**
             * Перевод строки.
             *
             * @var 'LF'|'CR'|'CRLF'
             */
            'end-of-line'              => 'CRLF',

            /**
             * Стиль отступов.
             *
             * @var 'INDENT_SPACE'|'INDENT_TAB'
             */
            'indent-style'             => self::INDENT_SPACE,

            /**
             * Размер отступов.
             *
             * @var int|'tab'
             */
            'indent-size'              => 4,

            /**
             * Удаление концевых пробелов.
             */
            'trim-trailing-whitespace' => true,
        );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static protected $_order
        = array(
            'A' => 5,
            'B' => 0,
            'C' => 0,
            'D' => 40,
            'E' => 0,
            'F' => 0,
        );


    // --- Стили отступов ---

    /** Пробелы. */
    const INDENT_SPACE = 'INDENT_SPACE';

    /** Табуляция. */
    const INDENT_TAB = 'INDENT_TAB';


    // --- Заменитель ---

    const REPLACER = 'CODE';


    // --- Открытые методы класса ---

    /**
     * @see \Typo\Module::validateOption()
     */
    public function validateOption($name, &$value)
    {
        switch ($name) {
            case 'end-of-line' :
                $value = mb_strtoupper($value);
                if (!in_array($value, array('LF', 'CR', 'CRLF'))) {
                    return self::throwException(Exception::E_OPTION_VALUE, "Недопустимое значение параметра '$name'");
                }
                break;

            case 'indent-style' :
                if (!Utility::validateConst(get_called_class(), $value, 'INDENT'))
                    return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный стиль отступов '$value'");
                break;

            case 'indent-size' :
                if (is_string($value)) {
                    $value = mb_strtolower($value);
                    if ($value !== 'tab')
                        $value = (int)$value;
                }

                if (is_int($value)) {
                    if ($value < 1)
                        return self::throwException(
                            Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть положительным целым числом или строкой 'tab'"
                            . var_dump($value)
                        );
                }
                break;

            default :
                Module::validateOption($name, $value);
        }
    }


    // --- Защищенные методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет все блоки &lt;code&gt;...&lt;/code&gt; на соответствующие заменители.
     */
    protected function stageA()
    {
        if (!$this->typo->_options['html-in-enabled'])
            return;

        $_this = $this;

        $rules = array(
            '~<\h*code((?:\h[^>]*)?)>(.*(?:[\n\r]|<br\h*/?>).*)</code>~iusU' => function ($m) use ($_this) {
                    $eol = str_replace(array('LF', 'CR'), array("\n", "\r"), $_this->getOption('end-of-line'));

                    $rules = array(
                        #A1 Оборачиваем каждую строку в тег code
                        '~\r\n|\n\r|[\n\r]|<br\h*/?>~' => '</code>' . $eol . '<code>',

                        #A2 Удаление концевых пробелов
                        'trim-trailing-whitespace'     => array(
                            '~[\t\h]+(?=</code>)~' => '',
                        ),
                    );

                    if ($_this->getOption('indent-style') === Code::INDENT_SPACE
                        && is_int(
                            $_this->getOption('indent-size')
                        )
                    ) {
                        $rules += array(
                            #A3 Заменяем табуляцию пробелами
                            '~(?<=<code>)([\t\h]+)~' => function ($m) use ($_this) {
                                    $size = $_this->getOption('indent-size');

                                    $replaces = array(
                                        '~\t~' => str_repeat(' ', $size),
                                        '~\h~' => ' ',
                                    );
                                    $spaces = preg_replace(array_keys($replaces), array_values($replaces), $m[1]);

                                    $count = floor(mb_strlen($spaces) / $size) * $size;

                                    return str_repeat(' ', $count);
                                },
                        );
                    } else {
                        $rules += array(
                            #A4 Заменяем пробелы табуляцией
                            '~(?<=<code>)([\t\h]*)\h{4}~' => '$1\t',
                            '~(?<=<code>)(\t*)\h+~'       => '$1',
                        );
                    }

                    $data = $_this->applyRules($rules, array(), "<pre{$m[1]}><code>{$m[2]}</code></pre>");

                    return $_this->text->pushStorage($data, Code::REPLACER, Typo::VISIBLE);
                },
        );

        $this->applyRules($rules);

        $this->text->preg_replace_storage('~<\h*code(\h[^>]*)?>.*<\/code>~isU', self::REPLACER, Typo::VISIBLE);
    }

    /**
     * Стадия C.
     *
     * Восстанавливает блоки &lt;code&gt;...&lt;/code&gt;.
     */
    protected function stageD()
    {
        $this->text->popStorage(self::REPLACER, Typo::VISIBLE);
    }
}