<?php

namespace Wheels\Typo\Module\Nobr;

use Wheels\Typo\Typo;
use Wheels\Typo\Module\AbstractModule;
use Wheels\Typo\Exception;

/**
 * Неразрывные конструкции.
 *
 * Обрамляет участки текста, в которых запрещён перенос на следующую строку,
 * в ситуациях, когда использование неразрывного пробела не уместно, либо не возможно.
 *
 * По умолчанию нерезрывные конструкции обрамляются в &lt;span style="word-spacing:nowrap;"&gt;...&lt;/span&gt;.
 * При желании вы можете использовать любой другой тег.
 */
class Nobr extends AbstractModule
{
    /**
     * @see \Wheels\Typo\Module::$default_options
     */
    static protected $_default_options
        = array(
            /**
             * Открывающий тег для неразрывных конструкций.
             *
             * @example <span style="word-spacing:nowrap;">
             * @example <span class="nowrap">
             * @example <nobr> (тег отсутствует в стандартах HTML)
             *
             * @var string
             */
            'open'  => '<span style="word-spacing:nowrap;">',

            /**
             * Закрывающий тег для неразрывных конструкций.
             *
             * @example </span>
             * @example </nobr>
             *
             * @var string
             */
            'close' => '</span>',
        );

    /**
     * @see \Wheels\Typo\Module::$order
     */
    static protected $_order
        = array(
            'A' => 0,
            'B' => 40,
            'C' => 0,
            'D' => 0,
            'E' => 0,
            'F' => 0,
        );

    /**
     * Тег для неразрывных конструкций.
     *
     * @var string[]
     */
    protected $tag;


    // --- Открытые методы ---

    /**
     * @see \Wheels\Typo\Module::validateOption()
     */
    public function validateOption($name, &$value)
    {
        switch ($name) {
            // Тег для неразрывных конструкций
            case 'open' :
            case 'close' :
                if (!is_string($value)) {
                    return self::throwException(
                        Exception::E_OPTION_VALUE,
                        "Значение параметра '$name' должно быть строкой, а не " . gettype($value)
                    );
                }

                $this->tag[$name] = $this->getTypo()->getText()->pushStorage($value, Typo::REPLACER, Typo::INVISIBLE);
                break;

            default :
                AbstractModule::validateOption($name, $value);
        }
    }


    // --- Защищенные методы ---

    /**
     * Стадия A.
     *
     * Заменяет номера телефонов на заменители.
     */
    protected function stageA()
    {
        $_this = $this;

        #A1 Объединение в неразрывные конструкции номеров телефонов
        $callback = function ($matches) use ($_this) {
            if (preg_match('~[^\d]~', $matches[0])) {
                $text = $_this->getTypo()->getText()->pushStorage($matches[0], Typo::REPLACER, Typo::VISIBLE);
                return $_this->nowrap($text);
            } else
                return $matches[0];
        };

        // @todo: сделать модуль Phone, и придумать способ их взаимодействия, чтобы регулярное выражение было только в одном месте использовано
        // Module\Phone::getRegex('phone');
        $pattern
            = '~(?<=\b)(?:\+?[1-9]\d{,3}(?:\h|\-)?)(?:(\d{3}|\(\d{3}\))(\h|\-)?\d{3}(\h|\-)?\d{2}(\h|\-)?\d{2}(\h?(?:доб\.?|x|ext\.?|добавочный|extension)\h?\d+)?)(?=\b)~iu';
        $this->getTypo()->getText()->preg_replace_callback($pattern, $callback);
    }

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки тегов неразрывных конструкций в тексте.
     *
     * @todo Объединение IP-адресов
     */
    protected function stageB()
    {
        $c = Typo::getChars('chr');

        $_this = $this;

        $rules = array(
            #B1 Объединение в неразрывные конструкции коротких слов разделенных дефисом
            '~(?<=\b){a}{1,4}\-{a}{1,4}(?=\b)~iu'                                                                                    => $this->nowrap(
                    '$0'
                ),

            #B2 Объединение в неразрывные конструкции процентов
            '~(?<=\b)\d+\h?%~u'                                                                                                      => $this->nowrap(
                    '$0'
                ),

            #B3 Объединение в неразрывные конструкции номеров и параграфов
            '~(?:№|' . $c['sect']
            . ')\h?\d+(?=\b)~u'                                                                                                      => $this->nowrap(
                    '$0'
                ),

            #B4 Объединение в неразрывные конструкции чисел
            // @todo вырезать телефоны
            '~(?<=\b)\d{1,3}(\h\d{3})+(?=\b)~u'                                                                                      => $this->nowrap(
                    '$0'
                ),

            #B5 Полупробел между числом и единицами измерения
            '~(?<=\b)(\d+)\h({m}|гр?|кг|ц|т|[кмгтпэзи]?(б(ит)?|флопс))(?=\b)~iu'                                                     => $this->nowrap(
                    '$0'
                ),

            #B Объединение сокращений P.S., P.P.S.
            '~(?<=\b)p\.\h?((p\.\h?)?)s\.~iu'                                                                                        => function (
                    $m
                ) use ($_this) {
                    return $_this->nowrap(mb_strtoupper($m[0]));
                },

            #B Объединение сокращений "и т. д.", "и т. п.", "в т. ч.", "т. к.", "т. е.", "и др.", "до н. э.", "ч. т. д.", "ю. ш.", ...
            '~(?<=\b)(и\hт\.\h?[пд]\.|в\hт\.\h?ч\.|т\.\h?[ке]\.|и\hдр\.|до\hн\.\h?э\.|ч\.\h?т\.\h?д\.|[юс]\.\h?ш\.|[зв]\.\h?д\.)~iu' => $this->nowrap(
                    '$0'
                ),

            # Привязка сокращения ГОСТ к номеру
            '~(?<=\b)гост\h?(\d+)(\-|' . $c['minus'] . '|' . $c['mdash']
            . ')(\d+)~iu'                                                                                                            => function (
                    $m
                ) use ($_this) {
                    return $_this->nowrap(mb_strtoupper($m[0]));
                },
        );

        $this->applyRules($rules);
    }

    protected function stageC()
    {
        // @todo: Удаление всех тегов <br> и замена неразрывных пробелов на обычные пробелы из неразрывных конструкций
    }

    /**
     * Оборачивает текст в теги неразрывной конструкции.
     *
     * @param string $text Текст неразрывной конструкции.
     *
     * @return string
     */
    public function nowrap($text)
    {
        return $this->tag['open'] . $text . $this->tag['close'];
    }
}