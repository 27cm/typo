<?php

namespace Typo;

class Utility
{
    /**
     * Функция ord() для мультибайтовых символов.
     *
     * @author ur001 <ur001ur001@gmail.com>, http://ur001.habrahabr.ru
     *
     * @param string $c Символ UTF-8.
     *
     * @return int Код символа.
     */
    static public function ord($c)
    {
        $h = ord($c{0});
        if ($h <= 0x7F)
            return $h;
        elseif ($h < 0xC2)
            return false;
        elseif ($h <= 0xDF)
            return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
        elseif ($h <= 0xEF)
            return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6 | (ord($c{2}) & 0x3F);
        elseif ($h <= 0xF4)
            return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12 | (ord($c{2}) & 0x3F) << 6 | (ord($c{3}) & 0x3F);
        else
            return false;
    }

    /**
     * Функция chr() для мультибайтовых символов.
     *
     * @author ur001 <ur001ur001@gmail.com>, http://ur001.habrahabr.ru
     *
     * @param int $c Код символа.
     *
     * @return string|bool Символ UTF-8.
     */
    static public function chr($c)
    {
        if ($c <= 0x7F)
            return chr($c);
        elseif ($c <= 0x7FF)
            return chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F);
        elseif ($c <= 0xFFFF)
            return chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F) . chr(0x80 | $c & 0x3F);
        elseif ($c <= 0x10FFFF)
            return chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F) . chr(0x80 | $c >> 6 & 0x3F) . chr(0x80 | $c & 0x3F);
        else
            return false;
    }

    /**
     * Определение кодировки текста.
     *
     * @author Егор Бабенко, http://patttern.blogspot.ru/
     *
     * @param string $text Текст.
     *
     * @return string Кодировка текста.
     */
    static public function detectCharset($text)
    {
        if(empty($text))
            return 'UTF-8';

        $utflower  = 7;
        $utfupper  = 5;
        $lowercase = 3;
        $uppercase = 1;
        $last_simb = 0;
        $charsets = array(
            'UTF-8'      => 0,
            'CP1251'     => 0,
            'KOI8-R'     => 0,
            'IBM866'     => 0,
            'ISO-8859-5' => 0,
            'MAC'        => 0,
        );
        for ($i = 0; $i < strlen($text); $i++)
        {
            $char = ord($text[$i]);

            // non-russian characters
            if ($char < 128 || $char > 256)
                continue;

            // UTF-8
            if (($last_simb==208) && (($char>143 && $char<176) || $char==129))
                $charsets['UTF-8'] += ($utfupper * 2);
            if ((($last_simb==208) && (($char>175 && $char<192) || $char==145))
                || ($last_simb==209 && $char>127 && $char<144))
                $charsets['UTF-8'] += ($utflower * 2);

            // CP1251
            if (($char>223 && $char<256) || $char==184)
                $charsets['CP1251'] += $lowercase;
            if (($char>191 && $char<224) || $char==168)
                $charsets['CP1251'] += $uppercase;

            // KOI8-R
            if (($char>191 && $char<224) || $char==163)
                $charsets['KOI8-R'] += $lowercase;
            if (($char>222 && $char<256) || $char==179)
                $charsets['KOI8-R'] += $uppercase;

            // IBM866
            if (($char>159 && $char<176) || ($char>223 && $char<241))
                $charsets['IBM866'] += $lowercase;
            if (($char>127 && $char<160) || $char==241)
                $charsets['IBM866'] += $uppercase;

            // ISO-8859-5
            if (($char>207 && $char<240) || $char==161)
                $charsets['ISO-8859-5'] += $lowercase;
            if (($char>175 && $char<208) || $char==241)
                $charsets['ISO-8859-5'] += $uppercase;

            // MAC
            if ($char>221 && $char<255)
                $charsets['MAC'] += $lowercase;
            if ($char>127 && $char<160)
                $charsets['MAC'] += $uppercase;

            $last_simb = $char;
        }
        arsort($charsets);

        return key($charsets);
    }

	/**
	 * Удаление кодов HTML из текста
	 *
	 * <code>
	 *  // Remove UTF-8 chars:
	 * 	$str = EMT_Lib::clear_special_chars('your text', 'utf8');
	 *  // ... or HTML codes only:
	 * 	$str = EMT_Lib::clear_special_chars('your text', 'html');
	 * 	// ... or combo:
	 *  $str = EMT_Lib::clear_special_chars('your text');
	 * </code>
	 *
	 * @param 	string $text
	 * @param   mixed $mode
     *
	 * @return 	string|bool
	 */
	static public function clear_special_chars($text, $mode = null)
	{
		if(is_string($mode)) $mode = array($mode);
		if(is_null($mode)) $mode = array('utf8', 'html');
		if(!is_array($mode)) return false;
		$moder = array();
		foreach($mode as $mod) if(in_array($mod, array('utf8','html'))) $moder[] = $mod;
		if(count($moder)==0) return false;

		foreach (self::$_charsTable as $char => $vals)
		{
			foreach ($mode as $type)
			{
				if (isset($vals[$type]))
				{
					foreach ($vals[$type] as $v)
					{
						if ('utf8' === $type && is_int($v))
						{
							$v = self::_getUnicodeChar($v);
						}
						if ('html' === $type)
						{
							if(preg_match("/<[a-z]+>/i",$v))
							{
								$v = self::safe_tag_chars($v, true);
							}
						}
						$text = str_replace($v, $char, $text);
					}
				}
			}
		}

		return $text;
	}

    /**
     * Создаёт HTML тег.
     *
     * @param string $name  Имя.
     * @param string $value Значение.
     * @param array $attrs  Атрибуты.
     *
     * @return string
     */
    static public function createElement($name, $value = null, array $attrs = array())
    {
        $a = '';
        foreach($attrs as $n => $v)
            $a .= ' ' . $n . '="' . $v . '"';

        if($name == 'img')
            return "<{$name}{$a}>";

        return "<{$name}{$a}>{$value}</{$name}>";
    }

    /**
     * Преобразование строки в кодировку UTF-8.
     *
     * @param string $string        Строка, которую необходимо преобразовать.
     * @param string $in_charset    Кодировка входной строки.
     *
     * @return string   Возвращает преобразованную строку или FALSE в случае возникновения ошибки.
     */
    public static function convertToUTF8($string, $in_charset)
    {
        $in_charset = strtoupper($in_charset);
        if($in_charset == 'UTF-8')
            return $string;

        $result = iconv($in_charset, 'UTF-8//IGNORE', $string);
        if($result === false)
            return Typo::throwException(Typo::E_OPTION_VALUE, "Кодировка '$in_charset' не поддерживается");

        return $result;
    }

    /**
     * Преобразование строки из UTF-8 в заданную кодировку.
     *
     * @link http://hello-world.pw/typo/manual/typo.utility.convertfromutf8.php
     *
     * @param string $string        Строка, которую необходимо преобразовать.
     * @param string $out_charset   Требуемая на выходе кодировка.
     *
     * @return string   Возвращает преобразованную строку или FALSE в случае возникновения ошибки.
     */
    public static function convertFromUTF8($string, $out_charset)
    {
        $out_charset = strtoupper($out_charset);
        if($out_charset == 'UTF-8')
            return $string;

        $result = iconv('UTF-8', $out_charset . '//TRANSLIT', $string);
        if($result === false)
            return Typo::throwException(Typo::E_OPTION_VALUE, "Кодировка '$out_charset' не поддерживается");

        return $result;
    }

    /**
     * Проверяет существование константы класса.
     *
     * @param string $class     Класс.
     * @param string $value     Имя константы.
     * @param string $prefix    Префикс.
     *
     * @return bool Вовзращает true, если константа класса с таким именем существует
     *              и имеет заданный префикс, иначе - false.
     */
    static public function validateConst($class, $name, $prefix = null)
    {
        return ((!isset($prefix) || preg_match('~^' . $prefix . '_.~', $name)) && defined("$class::$name"));
    }

    /**
     * Add charset to ensure \DOMDocument::loadHTML() does not default to
     * ISO-8859-1 - this is a placeholder only workable on fragments for now.
     *
     * TODO: Replace this quickie stub with a complete functional version
     *
     * @param string $html
     * @param string $encoding
     */
//    static public function insertCharset($html, $encoding)
//    {
//        // Улучшить, добавлять <meta> в уже готовый html
//        if(preg_match('/^<html/i', $html))
//            return $html;
//        $encoding = strtoupper($encoding);
//        $html = <<<HTML
//<html><head><meta http-equiv="Content-Type" content="text/html; charset=$encoding">
//</head><body>$html</body></html>
//HTML;
//        return $html;
//    }
}
