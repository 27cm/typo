<?php

namespace Wheels\Typo\Module\Punct;

use Wheels\Typo;
use Wheels\Typo\Module;

/**
 * Дефисы и тире.
 *
 * Расставляет дефисы, тире и мягкие переносы в тексте.
 */
class Dash extends Module
{
    /**
     * @see \Wheels\Typo\Module::$default_options
     */
    protected $default_options = array(
        /**
         * Автоматическая расстановка дефисов.
         *
         * @var bool
         */
        'auto' => true,

        /**
         * Расстановка мягких переносов (мест возможного переноса) в словах.
         *
         * @link http://shy.dklab.ru/new/js/Autohyphen.htc
         *
         * @var bool
         */
        'hyphenation' => false,
    );

    /**
     * @see \Wheels\Typo\Module::$order
     */
    static public $order = array(
        'A' => 0,
        'B' => 20,
        'C' => 5,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Защищенные методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки дефисов и тире в тексте.
     */
    protected function stageB()
    {
        $s =& Typo::$chars['chr'];

        $rules = array(
            #B1 Замена дефиса, окруженного пробелами, на тире
            '~({a}{t}*\h)\-{1,3}(?=\h)~u' => '$1' . $s['ndash'],

            #B2 Тире после кавычек, скобок и пунктуации
            '~([:)",]{t}*\h?)\-{1,3}(?=\h)~u' => '$1' . $s['ndash'],

            #B3 Тире после переноса строки
			'~((?:[\n\r]|^)(?:{t}|\h)*)\-{1,3}(?=\h)~u' => '$1' . $s['mdash'],

            #B4 Тире после точки, троеточия, восклицательного и вопросительного знаков
			'~((?:[?!.]|' . $s['hellip'] . ')\h)-{1,3}(?=\h)~u' => '$1' . $s['ndash'],

            // Автоматическая расстановка дефисов.
            'auto' => array(
                #B6 Расстановка дефисов в предлогах "из-за", "из-под"
				'~(?<=\b)(из)\h?(за|под)(?=\b)~iu' => '$1-$2',

                #B7 Автоматическая простановка дефисов в неопределённых местоимениях
                '~(?<=\b)(кто|кем|когда|зачем|почему|как|что|чем|где|чего|кого|куда|кому|какой)\h?(то|либо|нибудь)(?=\b)~iu' => '$1-$2',
                '~(?<=\b)(ко[ей])\h?(кто|кем|когда|зачем|почему|как|что|чем|где|чего|кого|куда|кому|какой)(?=\b)~iu' => '$1-$2',
                '~(?<=\b)(вс[её])\h?(таки)(?=\b)~iu' => '$1-$2',
                '~(?<=\b)([а-яё]+)\h(ка|де|тка|кась|тка|тко|ткась)(?=\b)~iu' => '$1-$2',
            ),
        );

        $this->applyRules($rules);
    }

    /**
     * Стадия C.
     *
     * Применяет правила для расстановки мягких переносов (мест возможного переноса) в тексте.
     * Автор успользуемого алгоритма - Дмитрий Котеров <ur001ur001@gmail.com>, http://dklab.ru
     */
    protected function stageC()
    {
        $с =& Typo::$chars['chr'];

        $helpers = array(
            // Гласные
            '{g}' => '[аеёиоуыэюяaeiouy]',

            // Согласные
            '{s}' => '[бвгджзклмнпрстфхцчшщbcdfghjklmnpqrstvwxz]',

            // Буквы й, ь, ъ
            '{x}' => '[йьъ]',
        );

        $rules = array(
            #C1 Расстановка мягких переносов (мест возможного переноса) в словах.
            'hyphenation' => array(
                '~(?<!\{\{\{|\[\[\[)(?<=\b){a}+(?=\b)(?!\}\}\}|\]\]\])~iu' => array(
                    '~({a}*{x})({a}{2,})~iu' => '$1' . $с['shy'] . '$2',
                    '~({a}*{g}{s}{2})({s}{2}{g}{a}*)~iu' => '$1' . $с['shy'] . '$2',
                    '~({a}*{g}{s}{2})({s}{g}{a}*)~iu' => '$1' . $с['shy'] . '$2',
                    '~({a}*{s}{g})({s}{g}{a}*)~iu' => '$1' . $с['shy'] . '$2',
                    '~({a}*{g}{s})({s}{g}{a}*)~iu' => '$1' . $с['shy'] . '$2',
                    '~({a}*{s}{g})({g}{2}{a}*)~iu' => '$1' . $с['shy'] . '$2',
                    '~({a}*{s}{g})({g}{a}{1,})~iu' => '$1' . $с['shy'] . '$2',
                ),
            ),
        );

        $this->applyRules($rules, $helpers);
    }
}