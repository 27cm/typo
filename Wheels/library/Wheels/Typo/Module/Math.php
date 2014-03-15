<?php

namespace Wheels\Typo\Module;

use Wheels\Typo;
use Wheels\Typo\Module;

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
    static protected $_default_options = array(
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
        $c =& Typo::$chars['chr'];

        $_this = $this;

        $rules = array(
            #B1 Минус перед числом
            '~\-(?=\h?\d+\b)~u' => $c['minus'],

            #B2 Замена "x" на символ умножения
            '~\b(\d+)\h?[xх]\h?(\d+)\b~iu' => '$1×$2',

            #B3 Дроби
            'frac' => array(
                '~(?<=\b)1/2(?=\b)~' => $c['frac12'],
                '~(?<=\b)1/4(?=\b)~' => $c['frac14'],
                '~(?<=\b)3/4(?=\b)~' => $c['frac34'],
            ),

            #B4 Математические знаки
            '~!=~' => $c['ne'],
            '~<=~' => $c['le'],
            '~>=~' => $c['ge'],
            '~\~=~' => $c['cong'],
            '~(\+-|-\+)~' => $c['plusmn'],

            #B7 Меры измерения в квадрате, в кубе
            // @todo: неразрывных пробел перед ед. измерения
            '~(?<=\b)(кв|куб)\.\h?(([изафпнмсдгкМГТПЭЗИ]|мк|да)?м|([yzafpnmcdhkMGTPEZY\xB5]|da)?m)(?=\b)~u' => function($m) use($_this, $c) {
                $d = ($m[1] == 'кв') ? '2' : '3';

                if($_this->typo->_options['html-out-enabled'])
                    return '$1' . $_this->sup($d);
                else
                    return $c['sup' . $d];
            },
            '~(?<=\b)(([изафпнмсдгкМГТПЭЗИ]|мк|да)?м|([yzafpnmcdhkMGTPEZY\xB5]|da)?m)([23])(?=\b)~u' => function($m) use($_this, $c) {
                $d = $m[2];

                if($_this->typo->_options['html-out-enabled'])
                    return '$1' . $_this->sup($d);
                else
                    return $c['sup' . $d];
            },
        );

        if($this->typo->_options['html-out-enabled'])
        {
            $rules += array(
                $c['sup1'] => $this->sup('1'),
                $c['sup2'] => $this->sup('2'),
                $c['sup3'] => $this->sup('3'),

                #B5 Надстрочный текст после символа ^
                '~\^(({a}|\d)+|\[({a}|\d)+\])(?=\b)~iu' => $this->sup('$1'),

                # Возведение в степень
                '~(({a}|{b}|\d){t}*)\*\*(\d+)(?=\b)~iu' => '$1' . $this->sup('$2'),

                # мм2, км2, ...

            );
        }
        else
        {
            $rules += array(
                #B9 Верхний индекс "1", "2", "3"
                '~(?:\^|\*\*)([1-3])\b~u' => function($m) use ($c) {
                    $key = 'sup' . $m[1];
                    return $c[$key];
                },
            );
        }

        $this->applyRules($rules);
    }

    /**
     * Оборачивает текст в теги &lt;sup&gt;&lt;small&gt;...&lt;small&gt;&lt;sup&gt;.
     *
     * @param string $text  Текст.
     *
     * @return string
     */
    public function sup($text)
    {
        static $tag = null;

        if(is_null($tag))
        {
            $tag = array(
                'open'  => $this->text->pushStorage('<sup><small>', Typo::REPLACER, Typo::INVISIBLE),
                'close' => $this->text->pushStorage('</small></sup>', Typo::REPLACER, Typo::INVISIBLE),
            );
        }

        return $tag['open'] . $text . $tag['close'];
    }

    /**
     * Оборачивает текст в теги &lt;sub&gt;&lt;small&gt;...&lt;small&gt;&lt;sub&gt;.
     *
     * @param string $text  Текст.
     *
     * @return string
     */
    public function sub($text)
    {
        static $tag = null;

        if(is_null($tag))
        {
            $tag = array(
                'open'  => $this->text->pushStorage('<sub><small>', Typo::REPLACER, Typo::INVISIBLE),
                'close' => $this->text->pushStorage('</small></sub>', Typo::REPLACER, Typo::INVISIBLE),
            );
        }

        return $tag['open'] . $text . $tag['close'];
    }
}