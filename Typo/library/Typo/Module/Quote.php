<?php

namespace Typo\Module;

use Typo;
use Typo\Module;
use Typo\Exception;

/**
 * Кавычки.
 *
 * Расставляет кавычки в тексте.
 */
class Quote extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array(
        /**
         * Открывающая кавычка.
         *
         * @var string
         */
        'quote-open' => 'laquo',

        /**
         * Закрывающая кавычка.
         *
         * @var string
         */
        'quote-close' => 'raquo',

        /**
         * Внутренняя открывающая кавычка.
         *
         * @var string
         */
        'subquote-open' => 'ldquo',

        /**
         * Внутренняя закрывающая кавычка.
         *
         * @var string
         */
        'subquote-close' => 'rdquo',
    );

    /**
     * Приоритет выполнения стадий.
     *
     * @var array
     */
    static public $order = array(
        'A' => 0,
        'B' => 30,
        'C' => 0,
        'D' => 0,
        'E' => 0,
        'F' => 0,
    );


    // --- Открытые методы класса ---

    /**
     * Проверка значения параметра (с возможной корректировкой).
     *
     * @param string $name      Название параметра.
     * @param mixed  $value     Значение параметра.
     *
     * @return void
     */
    public function validateOption($name, &$value)
    {
        switch($name)
        {
            // Кавычки
            case 'quote-open' :
            case 'quote-close' :
            case 'subquote-open' :
            case 'subquote-close' :
                if(!array_key_exists($value, Typo::$chars['chr']))
                    return self::throwException(Exception::E_OPTION_VALUE, "Неизвестный символ '&{$value};' (параметр '$name')");
            break;

            default : Module::validateOption($name, $value);
        }
    }


    // --- Защищенные методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки кавычек в тексте.
     *
     * @return void
     */
    protected function stageB()
    {
        $s =& Typo::$chars['chr'];

        $q1 = array(
            'open'  => $s[$this->options['quote-open']],
            'close' => $s[$this->options['quote-close']],
        );
        $q2 = array(
            'open'  => $s[$this->options['subquote-open']],
            'close' => $s[$this->options['subquote-close']],
        );

        $rules = array(
            // Открывающая кавычка
            '~((?:^|\(|\h){t}*)\"(?=\h?{t}*\S)~iu' => '$1' . $q1['open'],

            // Закрывающая кавычка
			'~({a}|{b}|[?!:.)]|' . $s['hellip'] . ')(\"+)(?={t}*(?:\h|[?!:;,.)]|' . $s['hellip'] . '|$))~u' => function ($m) use($q1) {
                return $m[1] . str_repeat($q1['close'], mb_strlen($m[2]));
            },

            // Закрывающая кавычка особые случаи
            '~([a-zа-яё0-9]|\.|' . $s['hellip'] . '|\!|\?|\>|\)|\:)((\"|\\\"|\&laquo\;)+)(\<[^\>]+\>)(\.|\&hellip\;|\;|\:|\?|\!|\,|\)|\<\/|$| )~iu' => function($m) use($q1) {
                return $m[1] . str_repeat($q1['close'], mb_substr_count($m[2],"\"") + mb_substr_count($m[2],"&laquo;")) . $m[4]. $m[5];
            },
            '~([a-zа-яё0-9]|\.|' . $s['hellip'] . '|\!|\?|\>|\)|\:)(\s+)((\"|\\\")+)(\s+)(\.|\&hellip\;|\;|\:|\?|\!|\,|\)|\<\/|$| )~iu' => function($m) use($q1) {
                return $m[1] .$m[2]. str_repeat($q1['close'], mb_substr_count($m[3],"\"") + mb_substr_count($m[3],"&laquo;")) . $m[5]. $m[6];
            },
        );

        $this->applyRules($rules);

        $level = 0;
		$offset = 0;
        $stack = array();

		while(true)
		{
            $p = $this->text->strpos($q1, $offset);

			if($p === false)
                break;

            list($pos, $str) = array_values($p);
            $offset = $pos + mb_strlen($str);

			if($str == $q1['open'])
			{
				if($level != 0)
                    $stack[] = array($q2['open'], $pos, mb_strlen($str));
				$level++;
			}
			else
			{
				$level--;
				if($level != 0)
                    $stack[] = array($q2['close'], $pos, mb_strlen($str));
			}

			if($level == 0)
			{
                $delta = 0;
                foreach($stack as $data)
                {
                    $this->text->substr_replace($data[0], $data[1] + $delta, $data[2]);
                    $delta += mb_strlen($data[0]) - $data[2];
                }
                $offset += $delta;
			}
		}
    }
}