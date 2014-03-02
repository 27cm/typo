<?php

namespace Typo\Module;

use Typo;
use Typo\Module;
use Typo\Exception;

/**
 * Неразрывные конструкции.
 *
 * Обрамляет участки текста, в которых запрещён перенос на следующую строку,
 * в ситуациях, когда использование неразрывного пробела не уместно, либо не возможно.
 *
 * По умолчанию нерезрывные конструкции обрамляются в <span style="word-spacing:nowrap">...</span>.
 * При желании вы можете использовать любой другой тег.
 */
class Nobr extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array(
        /**
         * Открывающий тег для неразрывных конструкций.
         *
         * @example <span style="word-spacing:nowrap">
         * @example <span class="nowrap">
         * @example <nobr> (тег отсутствует в стандартах HTML)
         *
         * @var string
         */
        'open' => '<span style="word-spacing:nowrap">',

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
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
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


    // --- Открытые методы класса ---

    /**
     * @see \Typo\Module::validateOption()
     */
    public function validateOption($name, &$value)
    {
        switch($name)
        {
            // Тег для неразрывных конструкций
            case 'open' :
            case 'close' :
                if(!is_string($value))
                    return self::throwException(Exception::E_OPTION_VALUE, "Значение параметра '$name' должно быть строкой, а не " . gettype($value));

                $this->tag[$name] = $this->text->pushStorage($value, Typo::REPLACER, Typo::INVISIBLE);
            break;

            default : Module::validateOption($name, $value);
        }
    }


    // --- Защищенные методы класса ---

    /**
     * Стадия A.
     *
     * Заменяет номера телефонов на заменители.
     * Работает со следующими форматами номеров:
     * +7 (495) 777-35-35
     * 8134-234-567-8901 доб. 87
     * +1(234)567-89-01 добавочный 1234
     * 1-234-567-8901 x1234
     * 1-234-567-8901 ext1234
     * 1-234-567-8901 extension 1234
     *
     * @return void
     */
    protected function stageA()
    {
        $_this = $this;

        #A1 Объединение в неразрывные конструкции номеров телефонов
        $callback = function($matches) use($_this) {
            if(preg_match('~[^\d]~', $matches[0]))
            {
                $text = $_this->text->pushStorage($matches[0], Typo::REPLACER, Typo::VISIBLE);
                return $_this->nowrap($text);
            }
            else
                return $matches[0];
        };

        $pattern = '~(?<=\b)(?:\+?[1-9]\d{,3}(?:\h|\-)?)(?:(\d{3}|\(\d{3}\))(\h|\-)?\d{3}(\h|\-)?\d{2}(\h|\-)?\d{2}(\h?(?:доб\.?|x|ext\.?|добавочный|extension)\h?\d+)?)(?=\b)~iu';
        $this->text->preg_replace_callback($pattern, $callback);
    }

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки тегов неразрывных конструкций в тексте.
     *
     * @todo Объединение IP-адресов
     *
     * @return void
     */
    protected function stageB()
    {
        $s =& Typo::$chars['chr'];

        $rules = array(
            #B1 Объединение в неразрывные конструкции коротких слов разделенных дефисом
            '~(?<=\b){a}{2,4}\-{a}{2,4}(?=\b)~iu' => $this->nowrap('$0'),

            #B2 Объединение в неразрывные конструкции процентов
            '~(?<=\b)\d+\h%~' => $this->nowrap('$0'),

            #B3 Объединение в неразрывные конструкции номеров и параграфов
            '~(?:№|' . $s['sect'] . ')\h\d+(?=\b)~' => $this->nowrap('$0'),

            #B4 Объединение в неразрывные конструкции чисел
            '~(?<=\b)\d{1,3}(\h\d{3})+(?=\b)~' => $this->nowrap('$0'),

            #B5 Полупробел между числом и единицами измерения
            '~(?<=\b)(\d+)\h({m}|гр?|кг|ц|т|[кмгтпэзи]?(б(ит)?|флопс))(?=\b)~iu' => $this->nowrap('$0'),
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
     * @param string $text  Текст неразрывной конструкции.
     *
     * @return string
     */
    public function nowrap($text)
    {
        return $this->tag['open'] . $text . $this->tag['close'];
    }
}