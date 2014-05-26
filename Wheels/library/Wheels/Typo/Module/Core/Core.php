<?php

namespace Wheels\Typo\Module\Core;

use Wheels\Typo\Module\Module;
use Wheels\Utility;

use Normalizer;

/**
 * Модуль-ядро типографа.
 *
 * Обрабатывает HTML-сущности.
 */
class Core extends Module
{
    /**
     * {@inheritDoc}
     */
    static protected $_order = array(
        'A' => 20,
        'B' => 0,
        'C' => 0,
        'D' => 0,
    );


    // --- Режимы кодирования спецсимволов ---

    /** Не кодировать. */
    const MODE_NONE = 'MODE_NONE';

    /** В виде имён. */
    const MODE_NAMES = 'MODE_NAMES';

    /** В виде кодов. */
    const MODE_CODES = 'MODE_CODES';

    /** В виде шестнадцатеричных кодов. */
    const MODE_HEX_CODES = 'MODE_HEX_CODES';


    // --- Открытые методы ---

    /**
     * Стадия A.
     *
     * - Исправляет ошибки в записе HTML-сущностей (лишние пробелы "&nbsp ;", забытые точки с запятой "&gt&thinsp", ведущие пробелы "&#000032;");
     * - Преобразует все HTML-сущности в соответствующие символы;
     * - Заменяет букву 'ё' на 'е';
     * - Заменяет неверные коды символов символом замены юникода (U+FFFD);
     * - Нормализует Unicode символы;
     * - Преобразует специальные символы в HTML-сущности.
     */
    public function stageA()
    {
        #A1 Исправление HTML-сущностей
        if ($this->getOption('html-entity-fix')) {
            $this->applyRulesPregReplace(array(
                '/(&([\da-z]+|#(\d+|x[\da-f]+)))\h+;/iu' => '$1;',
                '/(&([\da-z]+|#(\d+|x[\da-f]+)))\b(?!;)/i' => '$1;',
                '/(&#)0+((0|[1-9]\d*);)/' => '$1$2',
                '/(&#x)0+((0|[1-9a-f][\da-f]*);)/i' => '$1$2',
            ));
        }

        $this->getTypo()->getText()->html_entity_decode();

        #A2 Замена букв 'ё' на 'е'
        if ($this->getOption('e-convert')) {
            $this->applyRulesReplace(array(
                'ё' => 'е',
                'Ё' => 'Е',
            ));
        }

        #A3 Замена неверных кодов символов символом замены юникода (U+FFFD)
        $this->applyRulesPregReplace(array(
            '/&([\da-z]+|#(\d+|x[\da-f]+));/i' => Utility::chr(65533),
        ));

        #A4 Нормализация Unicode
        // @link http://habrahabr.ru/post/45489/
        if ($this->getOption('normalize')) {
            // @bug: № заменяется на No
            $this->getTypo()->getText()->normalize(Normalizer::FORM_KD);
            $this->applyRulesReplace(array(
                "\xD0\xB5\xCC\x88" => 'ё',
                "\xD0\x95\xCC\x88" => 'Ё',
                "\xD0\xB8\xCC\x86" => 'й',
                "\xD0\x98\xCC\x86" => 'Й',
            ));
        }

        $this->getTypo()->getText()->htmlspecialchars();
    }

    /**
     * Стадия B.
     */
    public function stageB()
    {
//        $mode = $this->getOption('encoding');
//
//        if ($mode !== self::MODE_NONE) {
//            $this->getTypo()->getText()->htmlentities(ENT_QUOTES);
//
//            $replace = array();
//            switch ($mode) {
//                case self::MODE_CODES :
//                    foreach (Typo::getChars('ord') as $ent => $ord) {
//                        $replace[$ent] = sprintf('&#%u;', $ord);
//                    }
//                break;
//                case self::MODE_HEX_CODES :
//                    foreach (Typo::getChars('ord') as $ent => $ord) {
//                        $replace[$ent] = sprintf('&#x%x;', $ord);
//                    }
//                break;
//                case self::MODE_NAMES :
//                    foreach (array_keys(Typo::getChars('chr')) as $ent) {
//                        $replace[$ent] = sprintf('&%s;', $ent);
//                    };
//                break;
//            }
//
//            $search = Typo::getChars('chr');
//            unset($search['amp']);
//            unset($replace['amp']);
//            $this->getTypo()->getText()->replace(array_values($search), array_values($replace));
//        }
    }
}