<?php

namespace Typo\Module;

use Typo;
use Typo\Module;

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
        'B' => 25,
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
                $value = strtolower($value);
                if(!array_key_exists($value, Typo::$chars))
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
        $s =& $this->typo->chr;

        $q1 = array(
            'open'  => $s[$this->options['quote-open']],
            'close' => $s[$this->options['quote-close']],
        );

        $rules = array(
            // Кавычки вне тэга <a>
            '~(\<%%\_\_[^\>]+\>)\"(.+?)\"(\<\/%%\_\_[^\>]+\>)~s' => '"$1$2$3"',

            // Открывающая кавычка
            '~(^|\(|\s|\>)(\"|\\\")(\S+)~iu' => '$1' . $q1['open'] . '$3',

            // Закрывающая кавычка
			'~([a-zа-яё0-9]|\.|\&hellip\;|\!|\?|\>|\)|\:)((\"|\\\")+)(\.|\&hellip\;|\;|\:|\?|\!|\,|\s|\)|\<\/|$)~ui' => function($m) use($q1) {
                return $m[1] . str_repeat($q1['close'], mb_substr_count($m[2],"\"") ) . $m[4];
            },

            // Закрывающая кавычка особые случаи
            '~([a-zа-яё0-9]|\.|\&hellip\;|\!|\?|\>|\)|\:)((\"|\\\"|\&laquo\;)+)(\<[^\>]+\>)(\.|\&hellip\;|\;|\:|\?|\!|\,|\)|\<\/|$| )~iu' => function($m) use($q1) {
                return $m[1] . str_repeat($q1['close'], mb_substr_count($m[2],"\"") + mb_substr_count($m[2],"&laquo;")) . $m[4]. $m[5];
            },
            '~([a-zа-яё0-9]|\.|\&hellip\;|\!|\?|\>|\)|\:)(\s+)((\"|\\\")+)(\s+)(\.|\&hellip\;|\;|\:|\?|\!|\,|\)|\<\/|$| )~iu' => function($m) use($q1) {
                return $m[1] .$m[2]. str_repeat($q1['close'], mb_substr_count($m[3],"\"") + mb_substr_count($m[3],"&laquo;")) . $m[5]. $m[6];
            },
            '~\>(\&laquo\;)\.($|\s|\<)~iu' => '>&raquo;.$2',

            // Открывающая кавычка особые случаи
            '~(^|\(|\s|\>)(\"|\\\")(\s)(\S+)~iu' => '$1' . $q1['open'] . '$4',
        );

        $this->applyRules($rules);
        // $this->subQuotes();
    }

    protected function subQuotes()
	{
        $s =& $this->typo->chr;

        $q1 = array(
            'open'  => $s[$this->options['quote-open']],
            'close' => $s[$this->options['quote-close']],
        );
        $q2 = array(
            'open'  => $s[$this->options['subquote-open']],
            'close' => $s[$this->options['subquote-close']],
        );

        $okposstack = array('0');
		$okpos = 0;
		$level = 0;
		$offset = 0;

        echo '<pre>';
        $i = 0;
		while($i < 5)
		{
            $i++;
            $p = $this->text->strpos($q1, $offset);
            var_dump($p);

			if($p === false)
                break;

			if($p['str'] == $q1['open'])
			{
				if($level > 0)
                    $this->text->substr_replace($q2['open'], $p['pos'], mb_strlen($p['str']));
				$level++;
			}
			else
			{
				$level--;
				if($level > 0)
                    $this->text->substr_replace($q2['close'], $p['pos'], mb_strlen($p['str']));;
			}

			$offset = $p['pos'] + mb_strlen($p['str']);
            echo 'offset = ' . $offset . '<br>';
			if($level == 0)
			{
				$okpos = $offset;
				array_push($okposstack, $okpos);
			}
            elseif($level < 0)
			{
				/*if(!$this->is_on('no_inches'))
				{
					do{
						$lokpos = array_pop($okposstack);
						$k = substr($this->_text, $lokpos, $offset-$lokpos);
						$k = str_replace(self::QUOTE_CRAWSE_OPEN, self::QUOTE_FIRS_OPEN, $k);
						$k = str_replace(self::QUOTE_CRAWSE_CLOSE, $q1['close'], $k);
						//$k = preg_replace("/(^|[^0-9])([0-9]+)\&raquo\;/ui", '\1\2&Prime;', $k, 1, $amount);

						$amount = 0;
						$__ax = preg_match_all("/(^|[^0-9])([0-9]+)\&raquo\;/ui", $k, $m);
						$__ay = 0;
						if($__ax)
						{
							$k = preg_replace_callback("/(^|[^0-9])([0-9]+)\&raquo\;/ui",
								create_function('$m','global $__ax,$__ay; $__ay++; if($__ay==$__ax){ return $m[1].$m[2]."&Prime;";} return $m[0];'),
								$k);
							$amount = 1;
						}



					} while(($amount==0) && count($okposstack));

					// успешно сделали замену
					if($amount == 1)
					{
						// заново просмотрим содержимое
						$this->_text = substr($this->_text, 0, $lokpos). $k . substr($this->_text, $offset);
						$offset = $lokpos;
						$level = 0;
						continue;
					}

					// иначе просто заменим последнюю явно на &quot; от отчаяния
					if($amount == 0)
					{
						// говорим, что всё в порядке
						$level = 0;
						$this->_text = substr($this->_text, 0, $p['pos']). '&quot;' . substr($this->_text, $offset);
						$offset = $p['pos'] + strlen('&quot;');
						$okposstack = array($offset);
						continue;
					}
				}*/
			}
		}

		// не совпало количество, отменяем все подкавычки
		/*if($level != 0 ){

			// закрывающих меньше, чем надо
			if($level>0)
			{
				$k = substr($this->_text, $okpos);
				$k = str_replace(self::QUOTE_CRAWSE_OPEN, self::QUOTE_FIRS_OPEN, $k);
				$k = str_replace(self::QUOTE_CRAWSE_CLOSE, $q1['close'], $k);
				$this->_text = substr($this->_text, 0, $okpos). $k;
			}
		}*/
    }
}