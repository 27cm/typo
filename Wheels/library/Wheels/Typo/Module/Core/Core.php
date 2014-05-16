<?php

namespace Wheels\Typo\Module\Core;

use Wheels\Typo\Module\AbstractModule;
use Wheels\Utility;

/**
 *
 */
class Core extends AbstractModule
{

    // --- Режимы кодирования спецсимволов ---

    /** Не кодировать. */
    const MODE_NONE = 'MODE_NONE';

    /** В виде имён. */
    const MODE_NAMES = 'MODE_NAMES';

    /** В виде кодов. */
    const MODE_CODES = 'MODE_CODES';

    /** В виде шестнадцатеричных кодов. */
    const MODE_HEX_CODES = 'MODE_HEX_CODES';

    /**
     * Стадия A.
     */
    public function stageA()
    {
        $rules = array(
            #A1 Убираем лишние пробелы в кодах символов
            '~(&(#\d+|[\da-z]+|#x[\da-f]+))\h+(?=\;)~iu' => '$1',

            #A2 Добавляем недостающие точки с запятой в кодах символов
            '~(&#\d+)(?![\;\d])~' => '$1;',
            '~(&[\da-z]+)(?![\;\da-z])~i' => '$1;',
            '~(&#x[\da-f]+)(?![\;\da-f])~i' => '$1;',

            #A3 Замена буквы 'ё' на 'е'
            'e-convert' => array(
                'ё' => 'е',
                'Ё' => 'Е',
            ),
        );
        $this->applyRules($rules);

        $this->getTypo()->getText()->html_entity_decode(ENT_QUOTES);

        $rules = array(
            #A1 Замена всех неизвестных символов на &#65533;
            '~&(#\d+|[\da-z]+|#x[\da-f]+)\;~i' => Utility::chr(65533),
        );
        $this->applyRules($rules);

//        if(!$this->options['html-in-enabled'])
//        {
//            $this->getTypo()->getText()->htmlspecialchars();
//        }
    }

    /**
     * Стадия B.
     */
//    public function stageB()
//    {
//        if ($this->getOption('encoding') !== self::MODE_NONE) {
//            $replace = array();
//            switch ($this->getOption('encoding')) {
//                case self::MODE_CODES :
//                    foreach (self::getChars('ord') as $ent => $ord) {
//                        $replace[$ent] = sprintf('&#%u;', $ord);
//                    }
//                    break;
//                case self::MODE_HEX_CODES :
//                    foreach (self::getChars('ord') as $ent => $ord) {
//                        $replace[$ent] = sprintf('&#x%x;', $ord);
//                    }
//                    break;
//                case self::MODE_NAMES :
//                    foreach (array_keys(self::getChars('chr')) as $ent) {
//                        $replace[$ent] = sprintf('&%s;', $ent);
//                    };
//                    break;
//            }
//
//            // @todo: Заменить все символы, не поддерживаемый выходной кодировкой на HTML-сущности
//
//            $search = self::getChars('chr');
//            unset($search['amp']);
//            unset($replace['amp']);
//            $this->getTypo()->getText()->replace(array_values($search), array_values($replace));
//        }
//    }

    /**
     * Стадия D.
     */
//    public function stageD()
//    {
//        $this->getTypo()->getText()->popStorage(self::REPLACER, self::INVISIBLE);
//        $this->getTypo()->getText()->popStorage(self::REPLACER, self::VISIBLE);
//
//        // Вставлять <br> перед каждым переводом строки
//        $this->getTypo()->getText()->preg_replace('~\n|\&NewLine\;~', '<br />');
//        ////if($this->options['nl2br'])
//        //   $this->getTypo()->getText()->nl2br();
//    }
}