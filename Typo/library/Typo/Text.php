<?php

namespace Typo;

use Typo\Module;
use Typo\Exception;
use Typo\Utility;

/**
 * Текст.
 */
class Text
{
    /**
     * Текст.
     *
     * @var string
     */
    protected $text;

    /**
     * Временное хранилище.
     *
     * @var array
     */
    protected $storage = array();


    // --- Конструктор ---

    /**
     * @param string $text  Текст.
     */
    public function __construct($text)
    {
        $this->text = (string) $text;
    }


    // --- Открытые методы класса ---

    /**
     * Преобразовывает объект в строку.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }

    /**
     * Помещает фрагмент текста в хранилище.
     *
     * @staticvar int $counters Счётчики замен.
     *
     * @param string $data          Фрагмент текста.
     * @param string $replacement   Строка для замены.
     *
     * @return string
     */
    public function pushStorage($data, $name, $replacement)
    {
        static $counters = array();

        $key = sprintf($replacement, $name, 0);
        if(!array_key_exists($key, $counters))
            $counters[$key] = 1;

        $i =& $counters[$key];

        // Предусматриваем случай, если в исходном тексте уже был данный ключ
        do
        {
            $k = sprintf($replacement, $name, $i);
            $i++;
        }
        while($this->strpos($k) !== false);

        $this->storage[$key][$k] = $data;

        return $k;
    }

    /**
     * Восстанавливает фрагмент текста из хранилища.
     *
     * @param string $replacement
     *
     * @uses \Typo\Text::replace()
     *
     * @return string
     */
    public function popStorage($name, $replacement, &$count = null)
    {
        $key = sprintf($replacement, $name, 0);

        if(!isset($this->storage[$key]) || empty($this->storage[$key]))
            return;

        $this->replace(array_keys($this->storage[$key]), array_values($this->storage[$key]), $count);

        if(!isset($count))
            unset($this->storage[$key]);
    }

    public function removeIPs()
    {
        $IPs = array();
        $replace = self::IP;

        $callback = function($matches) use(&$IPs, $replace)
        {
            $IP = $matches[0];

            $IPs[] = Text::NOBR_OPEN . $IP . Text::NOBR_CLOSE;

            return $replace;
        };

        $this->preg_replace_callback('~(?:(?:25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(?:25[0-5]|2[0-4]\d|[01]?\d\d?)(?![\wа-яё])~iu', $callback);
        $this->storage[$replace] = $IPs;
    }

    /**
     * Определение кодировки текста.
     *
     * @return string Кодировка текста.
     */
    public function detectCharset()
    {
        return Utility::detectCharset($this->text);
    }

    /**
     * Преобразовывает текст в требуемую кодировку.
     *
     * @param string $in_charset    Кодировка входной строки.
     * @param string $out_charset   Требуемая на выходе кодировка.
     *
     * @throw \Typo\Exception
     *
     * @return void
     */
    public function iconv($in_charset, $out_charset)
    {
        $result = iconv($in_charset, $out_charset, $this->text);
        if($result === FALSE)
            return Module::throwException(Exception::E_UNKNOWN, "Не удалось изменить кодировку текста с '$in_charset' на '$out_charset'");
        else
            $this->text = $result;
    }

    /**
     * Заменяет все вхождения строки поиска на строку замены.
     *
     * @param string|string[] $search   Искомое значение.
     * @param string|string[] $replace  Значение замены.
     * @param int $count                Если передан, то будет установлен в количество произведенных замен.
     *
     * @return void
     */
    public function replace($search, $replace, &$count = null)
    {
        if(is_string($search) && is_array($replace))
        {
            $count = 0;
            $length = strlen($search);
            while(($pos = $this->strpos($search)) !== false)
            {
                $this->substr_replace(array_shift($replace), $pos, $length);
                $count++;
            }
        }
        else
            $this->text = str_replace($search, $replace, $this->text, $count);
    }

    /**
     * Заменяет все вхождения строки поиска на строку замены с использованием callback-функции.
     *
     * @param string|string[] $search   Искомое значение.
     * @param callable        $callback Вызываемая callback-функция, которой будет передано найденное значение;
     *                                  callback-функция должна вернуть строку с заменой.
     *
     * @return void
     */
    public function replace_callback($search, $callback)
    {
        if (is_array($search)) {
            $searchArray = $search;
        }
        else if (is_string($search))
            $searchArray = array($search);

        foreach($searchArray as $searchValue) {
            $length = mb_strlen($searchValue);
            $count = 0;
            $replacedLength = 0;
            $pos = 0;
            while(($pos = $this->strpos($searchValue, $pos + $replacedLength)) !== false)
            {
                $replaced = $callback($this->substr($pos, $length));
                $this->substr_replace($replaced, $pos, $length);
                $replacedLength = mb_strlen($replaced);
                $count++;
            }
        }
    }

    public function substr ($start, $length = NULL)
    {
        return mb_substr($this->text, $start, $length);
    }

    /**
     * Заменяет часть строки.
     *
     * @param string $replacement Строка замены.
     * @param int $start          Если start положителен, замена начинается с символа с порядковым номером start в тексте.
     *                            Если start отрицателен, замена начинается с символа с порядковым номером start, считая от конца текста.
     * @param int $length         Если аргумент положителен, то он представляет собой длину заменяемой подстроки в тексте.
     *                            Если этот аргумент отрицательный, он определяет количество символов от конца текста, на которых заканчивается замена.
     *
     * @return void
     */
    public function substr_replace($replacement, $start, $length = null)
    {
        $this->text = mb_substr_replace($this->text, $replacement, $start, $length);
    }

    /**
     * Возвращает позицию первого вхождения подстроки.
     *
     * @param mixed $needle Если не является строкой, то приводится к целому и трактуется как код символа.
     * @param int $offset   Если этот параметр указан, то поиск будет начат с указанного количества символов с начала строки.
     *
     * @return int|bool Возвращает позицию, в которой находится искомая строка, относительно начала текста (независимо от смещения offset).
     *                  Также обратите внимание на то, что позиция строки отсчитывается от 0, а не от 1.
     *                  Возвращает FALSE, если искомая строка не найдена.
     */
    // @todo: doc fix
    public function strpos($needle, $offset = 0)
    {
        if(is_array($needle))
    	{
    		$m = false;
    		$w = false;
    		foreach($needle as $n)
    		{
    			$p = mb_strpos($this->text, $n, $offset);

    			if($p === false)
                    continue;

    			if($m === false || $p < $m)
    			{
    				$m = $p;
    				$w = $n;
    			}

    			if($m === false)
                    continue;
    		}
    		if($m === false)
                return false;

    		return array('pos' => $m, 'str' => $w);
    	}
        return mb_strpos($this->text, $needle, $offset);
    }

    /**
     * Выполняет поиск и замену по регулярному выражению.
     *
     * @param string|string[] $pattern      Искомый шаблон.
     * @param string|string[] $replacement  Строка или массив строк для замены.
     * @param int             $limit        Максимально возможное количество замен для каждого шаблона;
     *                                      по умолчанию равно -1 (без ограничений).
     * @param int             $count        Количество произведенных замен.
     *
     * @return void
     */
    public function preg_replace($pattern, $replacement, $limit = -1, &$count = null)
    {
        $this->text = preg_replace($pattern, $replacement, $this->text, $limit, $count);
    }

    /**
     * Выполняет поиск по регулярному выражению и замену с использованием callback-функции.
     *
     * @param string|string[] $pattern  Искомый шаблон.
     * @param callable        $callback Вызываемая callback-функция, которой будет передан массив совпавших элементов текста;
     *                                  callback-функция должна вернуть строку с заменой.
     * @param int             $limit    Максимально возможное количество замен для каждого шаблона;
     *                                  по умолчанию равно -1 (без ограничений).
     * @param int             $count    Количество произведенных замен.
     *
     * @return void
     */
    public function preg_replace_callback($pattern, $callback, $limit = -1, &$count = null)
    {
        $this->text = preg_replace_callback($pattern, $callback, $this->text, $limit, $count);
    }

    /**
     * Выполняет глобальный поиск шаблона в тексте.
     *
     * @param string $pattern   Искомый шаблон.
     * @param array $matches    Параметр flags регулирует порядок вывода совпадений в возвращаемом многомерном массиве.
     * @param string $flags
     * @param int $offset       Обычно поиск осуществляется слева направо, с начала строки.
     *                          Дополнительный параметр offset может быть использован для указания альтернативной начальной позиции для поиска.
     *
     * @return type             Возвращает количество найденных вхождений шаблона (которое может быть и нулем) либо FALSE,
     *                          если во время выполнения возникли какие-либо ошибки.
     */
    public function preg_match_all($pattern, array &$matches = null, $flags = 'PREG_PATTERN_ORDER', $offset = 0)
    {
        return preg_match_all($pattern, $this->text, $matches, $flags, $offset);
    }

    /**
     * Выполняет поиск и замену по регулярному выражению с помещением найденных фрагментов в хранилище.
     *
     * @param string|string[] $pattern      Искомый шаблон.
     * @param string          $replacement  Строка для замены.
     * @param int             $limit        Максимально возможное количество замен для каждого шаблона;
     *                                      по умолчанию равно -1 (без ограничений).
     * @param int             $count        Количество произведенных замен.
     *
     * @uses \Typo\Text::pushStorage()
     * @uses \Typo\Text::preg_replace_callback()
     *
     * @return void
     */
    public function preg_replace_storage($pattern, $name, $replacement, $limit = -1, &$count = null)
    {
        $_this = $this;
        $callback = function($matches) use(&$_this, $name, $replacement) {
            return $_this->pushStorage($matches[0], $name, $replacement);
        };
        $this->preg_replace_callback($pattern, $callback, $limit, $count);
    }

    /**
     *
     * @param type $flags
     * @param type $encoding
     * @param type $double_encode
     *
     * @return void
     */
    public function htmlentities($flags = 'ENT_COMPAT | ENT_HTML401', $encoding = 'UTF-8', $double_encode = true)
    {
        $this->text = htmlentities($this->text, $flags, $encoding, $double_encode);
    }

    /**
     * Вставляет HTML-код разрыва строки перед каждым переводом строки.
     *
     * @param bool $is_xhtml    Использовать ли совместимые с XHTML переводы строк или нет.
     *
     * @return void
     */
    public function nl2br($is_xhtml = true)
    {
        $this->text = nl2br($this->text, $is_xhtml);
    }
}