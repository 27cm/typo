<?php

namespace Wheels\Typo\Module;

use Wheels\Typo;
use Wheels\Typo\Module;

/**
 * Пробелы.
 *
 * Расставляет и удаляет простые и неразрывные пробелы в тексте.
 */
class Space extends Module
{
    /**
     * @see \Wheels\Typo\Module::$default_options
     */
    static protected $default_options = array(
        /**
         * Принудительная замена.
         *
         * @var bool
         */
        'nessesary' => true,

        /**
         * Расстановка неразрывных пробелов.
         *
         * @var bool
         */
        'nbsp' => true,

        /**
         * Расстановка тонких шпаций.
         *
         * @var bool
         */
        'thinsp' => true,

        /**
         * Удаление лишних пробелов.
         *
         * @var bool
         */
        'remove' => true,
    );

    /**
     * @see \Wheels\Typo\Module::$order
     */
    static public $order = array(
        'A' => 0,
        'B' => 25,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Защищенные методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки пробелов в тексте.
     *
     * @link http://habrahabr.ru/post/23250/
     * @link http://ru.wikipedia.org/wiki/%D0%9F%D1%80%D0%BE%D0%B1%D0%B5%D0%BB
     *
     * @return void
     */
    protected function stageB()
    {
        $c =& Typo::$chars['chr'];

        $helpers = array(
            // Открывающая кавычка
            '{lquot}' => '(?:"|' . $c['laquo'] . '|' . $c['ldquo'] . '|' . $c['lsquo'] . '|' . $c['bdquo'] . '|' . $c['lsaquo'] . ')',
        );

        $rules = array(
            # Принудительная замена
            'nessesary' => array(
                '~\h~u' => ' ',
            ),

            #B1 Добавление пропущенных пробелов после закрывающей и перед открывающей скобками
            '~((?:\)|{a}|{b}){t}*)\h*({t}*)\(~u' => '$1 $2(',
            '~\)({t}*)\h*({t}*(?:\(|{a}|{b}))~u' => ')$1 $2',

            // Расстановка неразрывных пробелов
            'nbsp' => array(
                #B2 Неразрывный пробел перед сокращениями px, dpi и lpi
                '~(?<=\d)\h(?=(px|dpi|lpi)\b)~iu' => $c['nbsp'],

                #B3 Неразрывный пробел перед двухсимвольными аббревиатурами
				'~(?<={a})\h(?={t}*[A-ZА-ЯЁ]{2}\b)~u' => $c['nbsp'],

                #B3 Неразрывный пробел после сокращений гл., стр., рис., илл., ил., ст., с., п.
                '~(?<=\b)((?:гл|стр|рис|табл|илл?|ст?|п)\.)\h(?=[XIV\d])~iu' => '$1' . $c['nbsp'],

                #B4 Неразрывный пробел после сокращений г., ул., пер., просп., пл., бул., наб., пр., ш., туп., см., им.
                '~(?<=\b)((?:г|ул|пер|просп|пл|бул|наб|пр|ш|туп|см|им)\.)\h~iu' => '$1' . $c['nbsp'],

                #B5 Неразрывный пробел после или перед сокращениями б-р, пр-кт
                '~(?<=\b)(б\-р|пр\-кт)\h~iu' => '$1' . $c['nbsp'],
                '~(?<=\b)\h(б\-р|пр\-кт)\b(?!\h)~iu' => $c['nbsp'] . '$1',

                #B6 Неразрывный пробел после века и года
                '~(?<=\b)([XIV]{1,5}|[12]\d|[1-9])\h?(?=(вв?|век)\b)~u' => '$1' . $c['nbsp'],
                '~(?<=\b)([1-9]\d{2,3})\h?(?=(год([ауе]|ом)?|гг?\.?)\b)~iu' => '$1' . $c['nbsp'],

                #B7 Неразрывный пробел после сокращений д., кв., эт.
                '~(?<=\b)((?:д|кв|эт)\.)\h(?=\d)~iu' => '$1' . $c['nbsp'],

                #B8 Неразрывный пробел в датах между числом и месяцем
                '~(?<=\d)\h*(?=января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря)~iu' => $c['nbsp'],

                #B9 Неразрывный пробел перед частицами
                '~\h(?=(же?|бы?|л[иь]|либо)\b)~iu' => $c['nbsp'],

                #B10 Неразрывные пробелы между инициалами и фамилией
                '~([А-ЯЁ]\.)\h([А-ЯЁ]\.)\h(?=[А-ЯЁ][а-яё])~u' => '$1' . $c['nbsp'] . '$2' . $c['nbsp'],
                '~([А-ЯЁ]\.)\h(?=[А-ЯЁ][а-яё])~u' => '$1' . $c['nbsp'],
                '~([А-ЯЁ][а-яё]+)\h([А-ЯЁ]\.)\h(?=[А-ЯЁ]\.)~u' => '$1' . $c['nbsp'] . '$2' . $c['nbsp'],
                '~([А-ЯЁ][а-яё]+)\h(?=[А-ЯЁ]\.)~u' => '$1' . $c['nbsp'],

                #B11 Неразрывный пробел в денежных суммах
                '~(?<=\d)\h((?:млн|тыс|млрд)\.?)\h(р(уб)?)(?=\b)~iu' => $c['nbsp'] . '$1' . $c['nbsp'] . '$2',
                '~(?<=\d)\h(?=(млн|тыс|млрд)\b)~iu' => $c['nbsp'],
                '~(?<=\d)\h(?=(р(уб)?|коп)\b)~iu' => $c['nbsp'],

                #B12 Неразрывный пробел в номере версии программы
                '~(?<=\b([vв]\.))\h(?=\d)~iu' => $c['nbsp'],

                #B13 Неразрывные пробелы в сокращениях "и т. д.", "и т. п.", "в т. ч.", "т. к.", "т. е.", "и др.", "до н. э.", "ч. т. д.", "ю. ш.", ...
                '~(?<=\bи)\h(т\.)\h?([дп]\.)~iu' => $c['nbsp'] . '$1' . $c['nbsp'] . '$2',
                '~(?<=\bв)\h(т\.)\h?(ч\.)~iu' => $c['nbsp'] . '$1' . $c['nbsp'] . '$2',
                '~(?<=\b)(т\.)\h?([ке]\.)~iu' => '$1' . $c['nbsp'] . '$2',
                '~(?<=\bи)\h(?=др\.)~iu' => $c['nbsp'],
                '~(?<=\bдо)\h(н\.)\h?(э\.)~iu' => $c['nbsp'] . '$1' . $c['nbsp'] . '$2',
                '~(?<=\bч\.)\h?(т\.)\h?(д\.)~iu' => $c['nbsp'] . '$1' . $c['nbsp'] . '$2',
                '~\h([юc]\.)\h?(ш\.)~iu' => $c['nbsp'] . '$1' . $c['nbsp'] . '$2',
                '~\h([зв]\.)\h?(д\.)~iu' => $c['nbsp'] . '$1' . $c['nbsp'] . '$2',

                #B14 Неразрывный пробел после тире, стоящего в начале строки, после точки, троеточия, восклицательного и вопросительного знаков
                '~((?:^|\n|[?!.]|' . $c['hellip'] . ')\h?(?:\-{1,3}|' . $c['ndash'] . '))\h~u' => '$1' . $c['nbsp'],

                #B15 Неразрывный пробел перед тире в остальных случаях
                '~\h(?=(\-{1,3}|' . $c['ndash'] . ')\h)~u' => $c['nbsp'],

                #B16 Неразрывный пробел перед последним словом в предложении
                '~(\h{t}*{a}{1,6}{t}*)\h(?={t}*{a}{1,6}{t}*(\n|$|[?!.]))~u' => '$1' . $c['nbsp'],

                #B17 Неразрывный пробел после предлогов, союзов и коротких слов
                '~(?<!' . $c['nbsp'] . ')(?<=\b)([a-zа-яё]{2}|а|в|и|к|о|с|у|я|вне|обо|ото|изо|под|подо|пред|предо|про|над|надо|как|без|безо|что|для|там|ещё|или|меж|между|перед|передо|около|через|сквозь|для|при)\h(?={a}{3,8})~iu' => '$1' . $c['nbsp'],

                # Неразрывный пробел между сокращением форм собственности и названием организации
				'~(?<=\b)(ООО|ЗАО|ОАО|НИИ|ПБОЮЛ)\h(?={t}*{lquot}?{t}*[A-ZА-ЯЁ])~u' => '$1' . $c['nbsp'],
            ),

            // Расстановка тонких шпаций
            'thinsp' => array(
                #B18 Тонкая шпация перед символом процента
                '~(?<=\b)(\d+)\h?(?=%)~u' => '$1' . $c['thinsp'],

                #B19 Тонкая шпация между символом номера или параграфа и числом
                '~(?<=№|' . $c['sect'] . ')\h?(?=\d)~u' => $c['thinsp'],

                #B20 Тонкая шпация между разрядами чисел
                '~(?<=\d)\h(?=\d{3})~u' => $c['thinsp'],

                #B21 Тонкая шпация между числом и единицами измерения
                '~(?<=\b)(\d+)\h?(?=({m}|гр?|кг|ц|т|[кмгтпэзи]?(б(ит)?|флопс))\b)~iu' => '$1' . $c['thinsp'],

                #B22 Тонкая шпация после числа градусов
                '~(?<=\b)(\d+)\h?(' . $c['deg'] . ')\h?(?=[CF]\b)~u' => '$1$2' . $c['thinsp'],
            ),

            // Удаление лишних пробелов
            'remove' => array(
                #B23 Удаление лишних пробелов между словами
                '~((?:{a}|{b}){t}*)(\h)(\h+)({t}*(?:{a}|{b}))~u' => '$1$2$4',

                #B Удаление пробелов в сокращениях P.S., P.P.S.
                '~(?<=\b)p\.\h?((p\.\h?)?)s\.~iu' => function($m) {
                    return (empty($m[1]) ? 'P.S.' : 'P.P.S.');
                },

                #B24 Удаление лишних пробелов после открывающей скобочки и перед закрывающей
                '~\(({t}*)\h+~u' => '($1',
                '~\h+({t}*)\)~u' => '$1)',

                #B25 Удаление лишних пробелов в конце текста
                '~\h+(?={t}*$)~u' => '',
            ),
        );

        $this->applyRules($rules, $helpers);
    }
}