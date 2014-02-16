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
    protected $default_options = array(
        /**
         * Дроби.
         *
         * @var bool
         */
        'frac' => true,
    );

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


    // --- Защищенные методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки чисел, дробей и математических знаков в тексте.
     *
     * @return void
     */
    protected function stageB()
    {
        $c =& $this->typo->chr;

        $rules = array(
            #B1 Минус перед числом
            '~\-(?=\h?\d+\b)~u' => $c['minus'],

            #B2 Замена "x" на символ умножения
            '~\b(\d+)\h?[xх]\h?(\d+)\b~iu' => '$1×$2',

            #B3 Дроби
            'frac' => array(
                '~(?<=\h)1/2(?=\b)~' => $c['frac12'],
                '~(?<=\h)1/4(?=\b)~' => $c['frac14'],
                '~(?<=\h)3/4(?=\b)~' => $c['frac34'],
            ),

            #B4 Математические знаки
            '~!=~' => $c['ne'],
            '~<=~' => $c['le'],
            '~>=~' => $c['ge'],
            '~\~=~' => $c['cong'],
            '~(\+-|-\+)~' => $c['plusmn'],
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
                #B5 Верхний индекс
                '~(?<={a})(\^|\*\*)(\d{1,3})\b~iu' => "{$sup['o']}$2{$sup['c']}",

                #B6 Нижний индекс
                '~(?<={a})_(\d{1,3})\b~iu' => "{$sub['o']}$1{$sub['c']}",

                #B7 Меры измерения в квадрате
                '~\sкв\.\h?({m})\b~u' => "{$c['nbsp']}$1{$sup['o']}2{$sup['c']}",

                #B8 Меры измерения в кубе
                '~\sкуб\.\h?({m})\b~u' => "{$c['nbsp']}$1{$sup['o']}3{$sup['c']}",
            );
        }
        else
        {
            $rules += array(
                #B9 Верхний индекс "1", "2", "3"
                '~(?<={a})(\^|\*\*)([1-3])\b~u' => function($m) use ($c) {
                    $key = 'sup' . $m[2];
                    return $c[$key];
                },

                #B10 Меры измерения в квадрате
                '~\sкв\.\h?({m})\b~u' => "{$c['nbsp']}$1{$c['sup2']}",

                #B11 Меры измерения в кубе
                '~\sкуб\.\h?({m})\b~u' => "{$c['nbsp']}$1{$c['sup3']}",
            );
        }

        $this->applyRules($rules);
    }
}