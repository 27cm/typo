<?php

namespace Module\Punct;

use ModuleTest;
use Wheels\Typo\Exception;
use Wheels\Typo\Module\Punct\Quote;

class QuoteTest extends ModuleTest
{
//    /**
//     * Использование неизвестных символов для кавычек.
//     *
//     * @dataProvider testUnknownQuoteProvider
//     */
//    public function testUnknownQuote($name)
//    {
//        $value = 'unknown';
//
//        $module = $this->typo->getModule('Wheels\Typo\Module\Punct\Quote');
//        $this->setExpectedException('Wheels\Typo\Exception', "Неизвестный символ '&{$value};' (параметр '$name')");
//        $module->setOption($name, $value);
//
//        // @todo третий параметр
//        $this->typo->setOption($name, $value, 'Wheels\Typo\Module\Punct\Quote');
//        $this->setOptions($options, 'Wheels\Typo\Module\Punct\Quote');
//
//        // @todo вставлять в $this->addModule(Module!!!)
//        // @todo configDir можно указать для любого модуля
//
//        $module->getOption($value);
//    }
//
//    public function testUnknownQuoteProvider()
//    {
//        return array(
//            array('quote-open'),
//            array('quote-close'),
//            array('subquote-open'),
//            array('subquote-close'),
//        );
//    }


}