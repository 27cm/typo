<?php

namespace Wheels\Typo\Module\Punct;

use Wheels\Typo\Module\Module;
use Wheels\Typo\Typo;
use Wheels\Utility;

/**
 * Пунктуация.
 *
 * Расставляет в тексте знаки препинания, кавычки, дефисы, мягкие переносы и многое другое.
 */
class Punct extends Module
{
    /**
     * {@inheritDoc}
     */
    static protected $_order = array(
        'A' => 0,
        'B' => 20,
        'C' => 5,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );

    /**
     * {@inheritDoc}
     */
    static protected $_helpers = array(
        // Знаки препинания
        '{p}' => '[!?:;,.]',

        // Гласные
        '{g}' => '[аеёиоуыэюяaeiouy]',

        // Согласные
        '{s}' => '(?:sh|ch|qu|[бвгджзклмнпрстфхцчшщbcdfghjklmnpqrstvwxz])',

        // Буквы й, ь, ъ
        '{x}' => '[йьъ]',
    );


    const REPLACER = 'WBR';

    // --- Режимы расстановки мягких переносов ---

    /** Не расставлять мягкие переносы. */
    const HYPEN_NONE = 'HYPEN_NONE';

    /** Использовать символ мягкого переноса &shy;. */
    const HYPEN_SHY = 'HYPEN_SHY';

    /** Использовать тег <wbr>. */
    const HYPEN_WBR = 'HYPEN_WBR';


    // --- Открытые методы ---

    /**
     * Стадия B.
     *
     * - Нормализует пунктуацию;
     * - Применяет правила расстановки знаков препинания, дефисов, тире и кавычек.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function stageB()
    {
        $c = Typo::getChars('chr');

        #B1 Нормализация пунктуации
        if ($this->getOption('normalize')) {
            $this->applyRulesReplace(array(
                // Длинное тире
                $c['mdash'] => '-',

                // Среднее тире
                $c['ndash'] => '-',

                // Минус
                $c['minus'] => '-',

                // Дефис-минус
                Utility::chr(45) => '-',

                // Неразрывный дефис
                Utility::chr(8209) => '-',

                // Цифровое тире
                Utility::chr(8210) => '-',

                // Кавычки
                $c['laquo']        => '"',
                $c['raquo']        => '"',
                $c['ldquo']        => '"',
                $c['rdquo']        => '"',
                $c['lsquo']        => '"',
                $c['rsquo']        => '"',
                $c['bdquo']        => '"',
                $c['sbquo']        => '"',
                $c['lsaquo']       => '<',
                $c['rsaquo']       => '>',
                Utility::chr(8223) => '"',
                Utility::chr(8219) => '"',
            ));
        }

        $this->applyRulesPregReplace(array(
            #B2 Замена двух знаков "..", "?.", "!." на три "...", "?..", "!.."
            '/((?:^|\n|\)|{a}|{b}|[\d"]){t}*[!?\.]{t}*)\.(?!{t}*{p})/u' => '$1..',

            #B3 Замена двух одинаковых знаков на один
            '/((?:^|\n|\)|{a}|{b}|[\d"]){t}*)([!?:;,])({t}*)\\2(?!{t}*{p})/u' => '$1$2$3$4',

            #B4 Замена "!?" на "?!"
            '/((?:^|\n|\)|{a}|{b}|[\d"]){t}*)\!({t}*)\?(?!{t}*{p})/u' => '$1?$2!',

            #B5 Оставляем 3 знака ("???", "?!!", "?.." и т. п.) вместо 4 или 5
            '/((?:^|\n|\)|{a}|{b}|[\d"]){t}*([!?.]{t}*){3})(?:[!?.]{t}*){1,2}(?!{p})/u' => '$1',

            #B6 Замена трёх точек на знак многоточия
            '/((?:^|\n|\)|{a}|{b}|[\d"]){t}*)\.{3}(?!{t}*{p})/u' => '$1' . $c['hellip'],

            #B7 Замена трёх точек на знак многоточия в лакунах
            '/(<|' . $c['lsaquo'] . ')(\.{2,3}|' . $c['hellip'] . ')(' . $c['rsaquo'] . '|>)/u' => $c['lsaquo'] . $c['hellip'] . $c['rsaquo'],

            #B8 Замена одиночной кавычки в слове на апостроф
            '/(?<={a})\'(?={a})/iu' => Utility::chr(700),

            #B9 Замена обратного апострофа после гласной на символ ударения (акут)
            '/(?<={g})\`/iu' => Utility::chr(769),

            #B10 Замена дефисов на тире
            '/((?:{a}|{b}){t}*\h{t}*)\-{1,3}(?={t}*(?:\h|{a}))/iu' => '$1' . $c['mdash'],
            '/([:)",]{t}*\h?)\-{1,3}(?={t}*(?:\h|{a}))/iu' => '$1' . $c['mdash'],
            '/((?:[\n\r]|^)(?:{t}|\h)*)\-{1,3}(?={t}*(?:\h|{a}))/iu' => '$1' . $c['mdash'],
            '/([?!.' . $c['hellip'] . ']{t}*(\h{t}*)?)-{1,3}(?={t}*(?:\h|{a}))/iu' => '$1' . $c['mdash'],
        ));

        // Автоматическая расстановка пунктуации
        if ($this->getOption('auto')) {
            $this->applyRulesPregReplace(array(
                #B11 Расстановка запятых перед союзами "а" и "но"
                '/((?:{a}|{b}){t}*(?:[!?' . $c['hellip'] . ']")?{t}*)\h((?:но|а)\h)/u' => '$1, $2',

                #B12 Добавление забытой точки в конце текста
                '/({a})(({t}|\h)*)$/u' => '$1.$2',

                #B13 Автоматическая расстановка дефисов в предлогах и неопределённых местоимениях
                '/(?<=\b)(из)\h?(за|под)(?=\b)/iu' => '$1-$2',
                '/(?<=\b)(кто|кем|когда|зачем|почему|как|что|чем|где|чего|кого|куда|кому|какой)\h?(то|либо|нибудь)(?=\b)/iu' => '$1-$2',
                '/(?<=\b)(ко[ей])\h?(кто|кем|когда|зачем|почему|как|что|чем|где|чего|кого|куда|кому|какой)(?=\b)/iu' => '$1-$2',
                '/(?<=\b)(вс[её])\h?(таки)(?=\b)/iu' => '$1-$2',
                '/(?<=\b)([а-яё]+)\h(ка|де|тка|кась|тка|тко|ткась)(?=\b)/iu' => '$1-$2',
            ));
        }

        #B14 Кавычки
        if ($this->getOption('quotes')) {
            $q1 = array(
                'open'  => $c[$this->getOption('quote-open')],
                'close' => $c[$this->getOption('quote-close')],
            );
            $q2 = array(
                'open'  => $c[$this->getOption('subquote-open')],
                'close' => $c[$this->getOption('subquote-close')],
            );

            $this->applyRulesPregReplace(array(
                '/((?:^|\(|\h){t}*)("(?:{t}*")*)(?={t}*[^\h"])/u' => function ($m) use ($q1) {
                    return $m[1] . str_replace('"', $q1['open'], $m[2]);
                },
                '/((?:{a}|{b}|[?!:.)' . $c['hellip'] . ']){t}*)("(?:{t}*")*)(?={t}*(?:$|\h|[?!:;,.)' . $c['hellip'] . ']))/iu' => function ($m) use ($q1) {
                    return $m[1] . str_replace('"', $q1['close'], $m[2]);
                },
            ));

            $level = 0;
            $offset = 0;
            $stack = array();

            while (true) {
                $p = $this->getTypo()->getText()->strpos($q1, $offset);

                if ($p === false)
                    break;

                list($pos, $str) = array_values($p);
                $offset = $pos + mb_strlen($str);

                if ($str == $q1['open']) {
                    if ($level % 2) {
                        $stack[] = array($q2['open'], $pos, mb_strlen($str));
                    }
                    $level++;
                } else {
                    $level--;
                    if ($level % 2) {
                        $stack[] = array($q2['close'], $pos, mb_strlen($str));
                    }
                }

                if ($level == 0) {
                    $delta = 0;
                    foreach ($stack as $data) {
                        $this->getTypo()->getText()->substr_replace($data[0], $data[1] + $delta, $data[2]);
                        $delta += mb_strlen($data[0]) - $data[2];
                    }
                    $offset += $delta;
                }
            }
        }
    }

    /**
     * Стадия C.
     *
     * - Применяет правила для расстановки мягких переносов (мест возможного переноса) в тексте;
     * - Заменяет теги <wbr> на {{{WBR1}}}, {{{WBR2}}}, ...
     *
     * Автор используемого алгоритма - Дмитрий Котеров <ur001ur001@gmail.com>, http://shy.dklab.ru/new/js/Autohyphen.htc
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function stageC()
    {
        #C1 Расстановка мягких переносов (мест возможного переноса) в словах
        if ($this->getOption('hyphenation') !== self::HYPEN_NONE) {
            $c = Typo::getChars('chr');

            switch ($this->getOption('hyphenation')) {
                case self::HYPEN_SHY :
                    $hyphen = $c['shy'];
                break;
                default :
                    $hyphen = $this->getTypo()->getText()->pushStorage('<wbr>', self::REPLACER, self::INVISIBLE);
                break;
            }

            $this->applyRulesPregReplace(array(
                '/(?<!\{\{\{|\[\[\[)(?<=\b){a}+(?=\b)(?!\}\}\}|\]\]\])/iu' => array(
                    '/({s}{g})({g}{a})/iu'       => '$1' . $hyphen . '$2',
                    '/({g}{s})({s}{g})/iu'       => '$1' . $hyphen . '$2',
                    '/({s}{g})({s}{g})/iu'       => '$1' . $hyphen . '$2',
                    '/({g}{s})({s}{2}{g})/iu'    => '$1' . $hyphen . '$2',
                    '/({g}{s}{2})({s}{g})/iu'    => '$1' . $hyphen . '$2',
                    '/({g}{s}{2})({s}{2}{g})/iu' => '$1' . $hyphen . '$2',
                    '/({x})({a}{2})/iu'          => '$1' . $hyphen . '$2',
                ),
            ));
        }
    }

    /**
     * Стадия D.
     *
     * - Восстанавливает теги <wbr>.
     *
     * @return void Этот метод не возвращает значения после выполнения.
     */
    public function stageD()
    {
        $this->getTypo()->getText()->popStorage(self::REPLACER, self::INVISIBLE);
    }
}
