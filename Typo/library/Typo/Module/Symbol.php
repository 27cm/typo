<?php

namespace Typo\Module;

use Typo\Module;

/**
 * Спецсимволы.
 *
 * Расставляет спецсимволы в тексте.
 */
class Symbol extends Module
{
    /**
     * Настройки по умолчанию.
     *
     * @var array
     */
    protected $default_options = array();

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


    // --- Защищенные методы класса ---

    /**
     * Стадия B.
     *
     * Применяет правила для расстановки кавычек в тексте.
     */
    protected function stageB()
    {
        $s =& $this->typo->chr;

        $rules = array(
            // Замена (tm) на символ торговой марки
            // Товарный знак (™)
            '~\s?\(tm|тм\)~iu' => '<sup>' . $s['trade'] . '</sup>',

            // Знак охраны авторского права (©)
            // @todo: пробелы должен убирать другой модуль
            '~\([cс]\)\s?~iu' => $s['copy'],

            // Знак правовой охраны товарного знака (®)
            '~\s?\(r\)~i' => "<sup><small>{$s['reg']}</small></sup>",

            // Расстановка правильного апострофа в текстах
			'/(\s|^|\>|\&rsquo\;)([a-zа-яё]{1,})\'([a-zа-яё]+)/ui' => '\1\2&rsquo;\3',

            'description'	=> 'Градусы по Фаренгейту',
            'pattern' 		=> '/([0-9]+)F($|\s|\.|\,|\;|\:|\&nbsp\;|\?|\!)/eu',
            'replacement' 	=> '"".$this->tag($m[1]." &deg;F","span", array("class"=>"nowrap")) .$m[2]',

            'description'	=> 'Символ евро',
            'simple_replace' => true,
            'pattern' 		=> '€',
            'replacement' 	=> '&euro;',

            'description'	=> 'Замена стрелок вправо-влево на html коды',
            'pattern' 		=> array('/(\s|\>|\&nbsp\;|^)\-\>($|\s|\&nbsp\;|\<)/', '/(\s|\>|\&nbsp\;|^|;)\<\-(\s|\&nbsp\;|$)/', '/→/u', '/←/u'),
            'replacement' 	=> array('\1&rarr;\2', '\1&larr;\2', '&rarr;', '&larr;' ),

            // Стрелки   -> => → [&rarr;], <- => ← [&larr;]
            '~ ->~' => '&nbsp;$1',
            '~<- ~' => '$1&nbsp;',

            // Промилле o/oo => ‰ [&permil;]
            '~\b(%|[oо0]\/[oо0])[oо0]\b~iu' => '‰',

            // Градусы 5 C = 5 °C
            '~(?<=\d\s)[CF]\b~' => '°$1',

            // Дюймы &Prime;

        );

        $this->applyRules($rules);

        $okposstack = array('0');
		$okpos = 0;
		$level = 0;
		$offset = 0;

		while(true)
		{
            $p = $this->text->strpos($q1, $offset);

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