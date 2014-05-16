<?php

namespace Wheels\Typo\Module\Align;

use Wheels\Typo\Typo;
use Wheels\Typo\Module\AbstractModule;

/**
 * Оптическое выравнивание.
 */
class Align extends AbstractModule
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    static protected $_default_options
        = array(
            /**
             * Открывающая кавычка.
             *
             * @example 'ldquo' (американский английский)
             * @example 'lsquo' (британский английский)
             * @example 'laquo' (русский)
             *
             * @var string
             */
            'quote-open'     => 'laquo',

            /**
             * Закрывающая кавычка.
             *
             * @example 'rdquo' (американский английский)
             * @example 'rsquo' (британский английский)
             * @example 'raquo' (русский)
             *
             * @var string
             */
            'quote-close'    => 'raquo',

            /**
             * Внутренняя открывающая кавычка.
             *
             * @example 'lsquo' (американский английский)
             * @example 'ldquo' (британский английский)
             * @example 'bdquo' (русский)
             *
             * @var string
             */
            'subquote-open'  => 'bdquo',

            /**
             * Внутренняя закрывающая кавычка.
             *
             * @example 'rsquo' (американский английский)
             * @example 'rdquo' (британский английский)
             * @example 'ldquo' (русский)
             *
             * @var string
             */
            'subquote-close' => 'ldquo',
        );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static protected $_order
        = array(
            'A' => 0,
            'B' => 10,
            'C' => 0,
            'D' => 0,
            'E' => 0,
            'F' => 0,
        );


    // --- Открытые методы ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки пунктуации в тексте.
     *
     * @return void
     */
    public function stageB()
    {
        $c = Typo::getChars('chr');

        $q1 = array(
            'open'  => $c[$this->getOption('quote-close')],
            'close' => $c[$this->getOption('quote-close')],
        );
        $q2 = array(
            'open'  => $c[$this->getOption('subquote-open')],
            'close' => $c[$this->getOption('subquote-close')],
        );

        $classes = array(
            'oa_obracket_sp_s' => "margin-right:0.3em;",
            "oa_obracket_sp_b" => "margin-left:-0.3em;",
            "oa_obracket_nl_b" => "margin-left:-0.3em;",
            "oa_comma_b"       => "margin-right:-0.2em;",
            "oa_comma_e"       => "margin-left:0.2em;",
            'oa_oquote_nl'     => "margin-left:-0.44em;",
            'oa_oqoute_sp_s'   => "margin-right:0.44em;",
            'oa_oqoute_sp_q'   => "margin-left:-0.44em;",
        );

        $rules = array(
            #B1 Оптическое выравнивание кавычек
            '~[' . $q1['open'] . $q2['open'] . ']~u' => $this->optAlignQuoteOpen('$0')
        );

        $rules = array(
            'oa_oquote'        => array(
                'description' => 'Оптическое выравнивание открывающей кавычки',
                'disabled'    => true,
                'pattern'     => array(
                    '/([a-zа-яё\-]{3,})(\040|\&nbsp\;|\t)(\&laquo\;)/uie',
                    '/(\n|\r|^)(\&laquo\;)/ei'
                ),
                'replacement' => array(
                    '$m[1] . $this->tag($m[2], "span", array("class"=>"oa_oqoute_sp_s")) . $this->tag($m[3], "span", array("class"=>"oa_oqoute_sp_q"))',
                    '$m[1] . $this->tag($m[2], "span", array("class"=>"oa_oquote_nl"))',
                ),
            ),
            'oa_obracket_coma' => array(
                'description' => 'Оптическое выравнивание для пунктуации (скобка и запятая)',
                'disabled'    => true,
                'pattern'     => array(
                    '/(\040|\&nbsp\;|\t)\(/ei',
                    '/(\n|\r|^)\(/ei',
                    '/([а-яёa-z0-9]+)\,(\040+)/iue',
                ),
                'replacement' => array(
                    '$this->tag($m[1], "span", array("class"=>"oa_obracket_sp_s")) . $this->tag("(", "span", array("class"=>"oa_obracket_sp_b"))',
                    '$m[1] . $this->tag("(", "span", array("class"=>"oa_obracket_nl_b"))',
                    '$m[1] . $this->tag(",", "span", array("class"=>"oa_comma_b")) . $this->tag(" ", "span", array("class"=>"oa_comma_e"))',
                ),
            ),
        );

        $this->applyRules($rules);
    }
}