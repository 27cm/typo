<?php

if(function_exists('mb_substr_replace') === false)
{
    /**
     * Заменяет часть строки.
     *
     * @param string $string        Входная строка.
     * @param string $replacement   Строка замены.
     * @param string $start         Если start положителен, замена начинается с символа с порядковым номером start строки string.
     *                              Если start отрицателен, замена начинается с символа с порядковым номером start, считая от конца строки string.
     * @param type $length          Если аргумент положителен, то он представляет собой длину заменяемой подстроки в строке string.
     *                              Если этот аргумент отрицательный, он определяет количество символов от конца строки string, на которых заканчивается замена.
     * @param type $encoding        Кодировка.
     *
     * @return string
     */
    function mb_substr_replace($string, $replacement, $start, $length = null, $encoding = null)
    {
        if (extension_loaded('mbstring') === false)
            return (is_null($length) === true) ? substr_replace($string, $replacement, $start) : substr_replace($string, $replacement, $start, $length);

        $string_length = (is_null($encoding) === true) ? mb_strlen($string) : mb_strlen($string, $encoding);

        if ($start < 0)
        {
            $start = max(0, $string_length + $start);
        }

        else if ($start > $string_length)
        {
            $start = $string_length;
        }

        if ($length < 0)
        {
            $length = max(0, $string_length - $start + $length);
        }

        else if ((is_null($length) === true) || ($length > $string_length))
        {
            $length = $string_length;
        }

        if (($start + $length) > $string_length)
        {
            $length = $string_length - $start;
        }

        if(is_null($encoding) === true)
        {
            return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length);
        }

        return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
    }
}

