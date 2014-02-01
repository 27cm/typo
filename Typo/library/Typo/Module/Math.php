<?php

namespace Typo\Module;

use Typo;
use Typo\Module;

/**
 * Числа, дроби и математические знаки.
 *
 * @link http://wikipedia.org/wiki/Punctuation
 */
class Math extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array();

    /**
     * Приоритет выполнения стадий
     *
     * @var array
     */
    static public $order = array(
        'A' => 0,
        'B' => 15,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Открытые методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки чисел, дробей и математических знаков в тексте.
     *
     * @return void
     */
    protected function stageB()
    {
        $s =& $this->typo->chars;

        $rules = array(
            // Минус перед числом
            '~\-(?=\h?\d+\b)~u' => $s['minus'],

            // Замена "x" на символ "×"
            '~\b(\d+)\h?[xх]\h?(\d+)\b~iu' => '$1×$2',

            // Дроби
            '~\b1/2\b~u' => $s['frac12'],
            '~\b1/4\b~u' => $s['frac14'],
            '~\b3/4\b~u' => $s['frac34'],

            // Математические знаки
            '~!=~' => $s['ne'],
            '~<=~' => $s['le'],
            '~>=~' => $s['ge'],
            '~\~=~' => $s['cong'],
            '~(\+-|-\+)~' => $s['plusmn'],

            // Полупробел между симоволом номера и числом
            '~(№|\&#8470\;)\h?(\d)~u' => $s['numb'] . $s['thinsp'] . '$2',

            // Полупробел между параграфом и числом
			'/(§|\&sect\;)\h?(\d)/ui' => $s['sect'] . $s['thinsp'] . '$2',
        );

        if($this->typo->options['html-out-enabled'])
        {
            $sup = array(
                'o' => $this->text->pushStorage('<sup><small>', Typo::REPLACER, Typo::INVISIBLE),
                'c' => $this->text->pushStorage('</small></sup>', Typo::REPLACER, Typo::INVISIBLE),
            );
            $sub = array(
                'o' => $this->text->pushStorage('<sub><small>', Typo::REPLACER, Typo::INVISIBLE),
                'c' => $this->text->pushStorage('</small></sub>', Typo::REPLACER, Typo::INVISIBLE),
            );

            $rules += array(
                // Верхний индекс
                '~(?<={a})\^(\d{1,3})\b~iu' => "{$sup['o']}$1{$sup['c']}",

                // Нижний индекс
                '~(?<={a})_(\d{1,3})\b~iu' => "{$sub['o']}$1{$sub['c']}",

                // Меры измерения в квадрате
                '~\sкв\.\h?({m})\b~u' => "{$s['nbsp']}$1{$sup['o']}2{$sup['c']}",

                // Меры измерения в кубе
                '~\sкуб\.\h?({m})\b~u' => "{$s['nbsp']}$1{$sup['o']}3{$sup['c']}",
            );
        }
        else
        {
            $rules += array(
                // Верхний индекс "1", "2", "3"
                '~(?<={a})\^(?<index>[123])\b~u' => function($m) use ($s) {
                    $key = 'sup' . $m['index'];
                    return $s[$key];
                },

                // Меры измерения в квадрате
                '~\sкв\.\h?({m})\b~u' => "{$s['nbsp']}$1{$s['sup2']}",

                // Меры измерения в кубе
                '~\sкуб\.\h?({m})\b~u' => "{$s['nbsp']}$1{$s['sup3']}",
            );
        }

        $this->applyRules($rules);
    }
}