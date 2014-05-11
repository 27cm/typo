<?php

namespace Wheels\Typo;

use Wheels\Typo\Module;
use Wheels\Utility;

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
     * Тип.
     *
     * @var string
     */
    public $type;

    /**
     * Кодировка текста.
     *
     * @var string
     */
    private $encoding;

    /**
     * Временное хранилище.
     *
     * @var array
     */
    protected $storage = array();


    // --- Типы ---

    /** HTML */
    const TYPE_HTML = 'TYPE_TEXT';

    /** Значение атрибута тега. */
    const TYPE_HTML_ATTR_VALUE = 'TYPE_HTML_ATTR_VALUE';


    // --- Конструктор ---

    /**
     * @param string $text     Текст.
     * @param string $type     Тип.
     * @param string $encoding Кодировка текста. Если не указана, то будет определена автоматически.
     */
    public function __construct($text = '', $type = self::TYPE_HTML, $encoding = null)
    {
        $this->setText($text, $encoding);
        $this->setType($type);
    }


    // --- Открытые методы ---

    /**
     * Устанавливает значение текста.
     *
     * @param string $value    Текст.
     * @param string $encoding Кодировка текста. Если не указана, то будет определена автоматически.
     *
     * @uses \Wheels\Utility::detectCharset()
     */
    public function setText($value, $encoding = null)
    {
        $this->text = (string)$value;

        if (is_null($encoding))
            $this->encoding = Utility::detectCharset($this->text);
        else {
            $this->encoding = $encoding;
        }
    }

    /**
     * Устанавливает тип.
     *
     * @param string $value Тип.
     */
    public function setType($value)
    {
        $this->type = $value;
    }

    /**
     * Вовзращает кодировку текста.
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    public function getText()
    {
        return $this->text;
    }

    /**
     * Помещает фрагмент текста в хранилище.
     *
     * @staticvar int $counters Счётчики замен.
     *
     * @param string $data     Фрагмент текста.
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     *
     * @return string
     */
    public function pushStorage($data, $replacer, $type)
    {
        static $counters = array();

        $key = sprintf($type, $replacer, 0);
        if (!array_key_exists($key, $counters))
            $counters[$key] = 1;

        $i =& $counters[$key];

        // Предусматриваем случай, если в исходном тексте уже был данный ключ
        do {
            $k = sprintf($type, $replacer, $i);
            $i++;
        } while ($this->strpos($k) !== false);

        $this->storage[$key][$k] = $data;

        return $k;
    }

    /**
     * Восстанавливает фрагмент текста из хранилища.
     *
     * @param string $replacer Имя строки для замены.
     * @param string $type     Тип заменителя.
     * @param int    $count    Если передан, то будет установлен в количество произведенных замен.
     *
     * @uses \Wheels\Typo\Text::replace()
     *
     * @return string
     */
    public function popStorage($replacer, $type, &$count = null)
    {
        $key = sprintf($type, $replacer, 0);

        if (!isset($this->storage[$key]) || empty($this->storage[$key]))
            return;

        $this->replace(array_keys($this->storage[$key]), array_values($this->storage[$key]), $count);
    }

    /**
     * Преобразовывает текст в требуемую кодировку.
     *
     * @param string $in_charset  Кодировка входной строки.
     * @param string $out_charset Требуемая на выходе кодировка.
     *
     * @throws \Wheels\Typo\Exception
     */
    public function iconv($in_charset, $out_charset)
    {
        $result = iconv($in_charset, $out_charset, $this->text);
        if ($result === false)
            return Module::throwException(
                Exception::E_UNKNOWN, "Не удалось изменить кодировку текста с '$in_charset' на '$out_charset'"
            );
        else
            $this->text = $result;
    }

    /**
     * Заменяет все вхождения строки поиска на строку замены.
     *
     * @param string|string[] $search  Искомое значение.
     * @param string|string[] $replace Значение замены.
     * @param int             $count   Если передан, то будет установлен в количество произведенных замен.
     */
    public function replace($search, $replace, &$count = null)
    {
        if (is_string($search) && is_array($replace)) {
            $count = 0;
            $length = strlen($search);
            while (($pos = $this->strpos($search)) !== false) {
                $this->substr_replace(array_shift($replace), $pos, $length);
                $count++;
            }
        } else
            $this->text = str_replace($search, $replace, $this->text, $count);
    }

    /**
     * Заменяет все вхождения строки поиска на строку замены с использованием callback-функции.
     *
     * @param string|string[] $search   Искомое значение.
     * @param callable        $callback Вызываемая callback-функция, которой будет передано найденное значение;
     *                                  callback-функция должна вернуть строку с заменой.
     */
    public function replace_callback($search, $callback)
    {
        if (is_array($search)) {
            $searchArray = $search;
        } else if (is_string($search))
            $searchArray = array($search);

        foreach ($searchArray as $searchValue) {
            $length = mb_strlen($searchValue);
            $count = 0;
            $replacedLength = 0;
            $pos = 0;
            while (($pos = $this->strpos($searchValue, $pos + $replacedLength)) !== false) {
                $replaced = $callback($this->substr($pos, $length));
                $this->substr_replace($replaced, $pos, $length);
                $replacedLength = mb_strlen($replaced);
                $count++;
            }
        }
    }

    /**
     * Возвращает подстроку.
     *
     * @param int $start    Если start неотрицателен, возвращаемая подстрока начинается с позиции start от начала текста, считая от нуля.
     *                      Если start отрицательный, возвращаемая подстрока начинается с позиции, отстоящей на start символов от конца текста.
     * @param int $length   Если length положительный, возвращаемая строка будет не длиннее length символов, начиная с параметра start.
     *                      Если length отрицательный, то будет отброшено указанное этим аргументом число символов с конца текста.
     *
     * @return type
     */
    public function substr($start, $length = null)
    {
        return mb_substr($this->text, $start, $length);
    }

    /**
     * Заменяет часть строки.
     *
     * @param string $replacement Строка замены.
     * @param int    $start       Если start положителен, замена начинается с символа с порядковым номером start в тексте.
     *                            Если start отрицателен, замена начинается с символа с порядковым номером start, считая от конца текста.
     * @param int    $length      Если аргумент положителен, то он представляет собой длину заменяемой подстроки в тексте.
     *                            Если этот аргумент отрицательный, он определяет количество символов от конца текста, на которых заканчивается замена.
     */
    public function substr_replace($replacement, $start, $length = null)
    {
        $this->text = mb_substr_replace($this->text, $replacement, $start, $length);
    }

    /**
     * Возвращает позицию первого вхождения подстроки.
     *
     * @param mixed $needle Если не является строкой или массивом, то приводится к целому и трактуется как код символа.
     *                      Если является массивом, то результатом будет ассоциативный массив с ключами:
     *                      'pos' - позиция первой найденной подстроки из массива needle;
     *                      'str' - значение первой наденной подстроки из массива needle;
     * @param int   $offset Если этот параметр указан, то поиск будет начат с указанного количества символов с начала текста.
     *
     * @return mixed    Возвращает позицию, в которой находится искомая строка, относительно начала текста (независимо от смещения offset).
     *                  Возвращает FALSE, если искомая строка не найдена.
     */
    public function strpos($needle, $offset = 0)
    {
        if (is_array($needle)) {
            $m = false;
            $w = false;
            foreach ($needle as $n) {
                $p = mb_strpos($this->text, $n, $offset);

                if ($p === false)
                    continue;

                if ($m === false || $p < $m) {
                    $m = $p;
                    $w = $n;
                }

                if ($m === false)
                    continue;
            }
            if ($m === false)
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
     */
    public function preg_replace_callback($pattern, $callback, $limit = -1, &$count = null)
    {
        $this->text = preg_replace_callback($pattern, $callback, $this->text, $limit, $count);
    }

    /**
     * Выполняет глобальный поиск шаблона в тексте.
     *
     * @param string $pattern   Искомый шаблон.
     * @param array  $matches   Параметр flags регулирует порядок вывода совпадений в возвращаемом многомерном массиве.
     * @param string $flags
     * @param int    $offset    Обычно поиск осуществляется слева направо, с начала строки.
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
     * @param string|string[] $pattern  Искомый шаблон.
     * @param string          $type     Строка для замены.
     * @param int             $limit    Максимально возможное количество замен для каждого шаблона;
     *                                  по умолчанию равно -1 (без ограничений).
     * @param int             $count    Количество произведенных замен.
     *
     * @uses \Wheels\Typo\Text::pushStorage()
     * @uses \Wheels\Typo\Text::preg_replace_callback()
     */
    public function preg_replace_storage($pattern, $replacer, $type, $limit = -1, &$count = null)
    {
        $_this = $this;
        $callback = function ($matches) use (&$_this, $replacer, $type) {
            return $_this->pushStorage($matches[0], $replacer, $type);
        };
        $this->preg_replace_callback($pattern, $callback, $limit, $count);
    }

    /**
     * Преобразует все возможные символы в соответствующие HTML-сущности.
     *
     * @param int  $flags           Битовая маска из флагов, определяющих режим обработки кавычек,
     *                              некорректных кодовых последовательностей и используемый тип документа.
     *                              По умолчанию используется ENT_COMPAT | ENT_HTML401.
     * @param bool $double_encode   При выключении параметра PHP не будет преобразовывать существующие html-сущности.
     *                              По умолчанию преобразуется все без ограничений.
     */
    public function htmlentities($flags = null, $double_encode = true)
    {
        if (is_null($flags))
            $flags = ENT_COMPAT /*| ENT_HTML401*/
            ;

        $this->text = htmlentities($this->text, $flags, $this->encoding, $double_encode);
    }

    /**
     * Преобразует специальные символы в HTML-сущности.
     *
     * @param int  $flags           Битовая маска из флагов, определяющих режим обработки кавычек,
     *                              некорректных кодовых последовательностей и используемый тип документа.
     *                              По умолчанию используется ENT_COMPAT | ENT_HTML401.
     * @param bool $double_encode   При выключении параметра PHP не будет преобразовывать существующие html-сущности.
     *                              По умолчанию преобразуется все без ограничений.
     */
    public function htmlspecialchars($flags = null, $double_encode = true)
    {
        if (is_null($flags))
            $flags = ENT_COMPAT /*| ENT_HTML401*/
            ;

        $this->text = htmlspecialchars($this->text, $flags, $this->encoding, $double_encode);
    }

    /**
     * Преобразует все HTML-сущности в соответствующие символы.
     *
     * @param int $flags            Битовая маска из флагов, определяющих режим обработки кавычек,
     *                              некорректных кодовых последовательностей и используемый тип документа.
     *                              По умолчанию используется ENT_COMPAT | ENT_HTML401.
     */
    public function html_entity_decode($flags = null)
    {
        if (is_null($flags))
            $flags = ENT_COMPAT /*| ENT_HTML401*/
            ;

        $this->text = html_entity_decode($this->text, $flags, $this->encoding);
    }

    /**
     * Вставляет HTML-код разрыва строки перед каждым переводом строки.
     *
     * @param bool $is_xhtml Использовать ли совместимые с XHTML переводы строк или нет.
     */
    public function nl2br($is_xhtml = true)
    {
        $this->text = nl2br($this->text, $is_xhtml);
    }

    /**
     * Преобразовывает объект в строку.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }
}