<?php

namespace Wheels\Typo\Module\Emoticon;

use Wheels\Typo\Module\Emoticon;

/**
 * Смайлы Skype.
 *
 * @link https://support.skype.com/en/faq/FA11046/what-are-emoticons
 * @link http://www.skype.rs/
 */
class Skype extends Emoticon
{
    /**
     * @see \Wheels\Typo\Module::$default_options
     */
    static protected $default_options = array(
        /**
         * HTML тег для изображений.
         *
         * @var string
         */
        'tag' => 'img',
        // @todo: путь к файлу для WINDOWS и UNIX

        /**
         * Ширина изображений смайликов.
         *
         * @var int
         */
        'width' => 20,

        /**
         * Высота изображений смайликов.
         *
         * @var int
         */
        'height' => 20,

        /**
         * Атрибуты.
         *
         * {id}       - номер;
         * {name}     - имя;
         * {title}    - название;
         * {emoticon} - эмотикон;
         * {width}    - ширина;
         * {height}   - высота.
         *
         * @var array
         */
        'attrs' => array(
            'src' => array(
                'value' => '/img/emoticons/skype/{id}.gif',
            ),
            'width' => array(
                'value' => '\Wheels\Typo\Module\Emoticon::attrWidth',
            ),
            'height' => array(
                'value' => '\Wheels\Typo\Module\Emoticon::attrHeight',
            ),
            'title' => array(
                'value' => '\Wheels\Typo\Module\Emoticon::attrTitle',
            ),
            'alt' => array(
                'name' => 'alt',
                'value' => '\Wheels\Typo\Module\Emoticon::attrAlt',
            ),
        ),
    );

    /**
     * @see \Typo\Module\Emoticon::$smiles
     */
    // http://smiles.spb.su/skype-all-smiles.php
    static public $smiles = array(
        array(
            'id'       => 1,
            'name'     => 'smile',
            'title'    => 'Улыбаюсь',
            'replaces' => array('(smile)', ':)', ':=)', ':-)'),
        ),
        array(
            'id'       => 2,
            'name'     => 'sad',
            'title'    => 'Грущу',
            'replaces' => array('(sad)', ':(', ':=(', ':-('),
        ),
        array(
            'id'       => 3,
            'name'     => 'laugh',
            'title'    => 'Смеюсь',
            'replaces' => array('(laugh)', '(lol)', '(LOL)', ':D', ':-D', ':=D', ':d', ':-d', ':=d', ':>', ':->'),
        ),
        array(
            'id'       => 4,
            'name'     => 'cool',
            'title'    => 'Крутой',
            'replaces' => array('(cool)', '8=)', '8-)', 'B=)', 'B-)'),
        ),
        array(
            'id'       => 5,
            'name'     => 'surprised',
            'title'    => 'Удивляюсь',
            'replaces' => array('(surprised)', ':o', ':=o', ':-o', ':O', ':=O', ':-O'),
        ),
        array(
            'id'       => 6,
            'name'     => 'wink',
            'title'    => 'Wink',
            'replaces' => array('(wink)', ';)', ';-)', ';=)'),
        ),
        array(
            'id'       => 7,
            'name'     => 'cry',
            'title'    => '',
            'replaces' => array('(cry)', ';(', ';-(', ';=(', ':\'('),
        ),
        array(
            'id'       => 8,
            'name'     => 'sweat',
            'title'    => '',
            'replaces' => array('(sweat)', '(:|'),
        ),
        array(
            'id'       => 9,
            'name'     => 'speechless',
            'title'    => '',
            'replaces' => array('(speechless)', ':|', ':=|', ':-|'),
        ),
        array(
            'id'       => 10,
            'name'     => 'kiss',
            'title'    => '',
            'replaces' => array('(kiss)', '(xo)', ':*', ':=*', ':-*'),
        ),
        array(
            'id'       => 11,
            'name'     => 'tongueout',
            'title'    => '',
            'replaces' => array('(tongueout)', ':P', ':=P', ':-P', ':p', ':=p', ':-p'),
        ),
        array(
            'id'       => 12,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(blush)', ':$', ':-$', ':=$', ':">'),
        ),
        array(
            'id'       => 13,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(wonder)', ':^)'),
        ),
        array(
            'id'       => 14,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(snooze)', '|-)', 'I-)', 'I=)'),
        ),
        array(
            'id'       => 15,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(dull)', '|(', '|-(', '|=('),
        ),
        array(
            'id'       => 16,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(inlove)', '(love)', ':]', ':-]'),
        ),
        array(
            'id'       => 17,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(grin)', ']:)', '>:)'),
        ),
        array(
            'id'       => 18,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(fingers)', '(yn)', '(fingerscrossed)', '(crossedfingers)'),
        ),
        array(
            'id'       => 19,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(yawn)'),
        ),
        array(
            'id'       => 20,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(puke)', ':&', ':-&', ':=&'),
        ),
        array(
            'id'       => 21,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(doh)'),
        ),
        array(
            'id'       => 22,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(angry)', ':@', ':-@', ':=@', 'x(', 'x-(', 'X(', 'X-(', 'x=(', 'X=(', ';@', ';-@'),
        ),
        array(
            'id'       => 23,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(wasntme)', '(wm)'),
        ),
        array(
            'id'       => 24,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(party)', '<O)', '<o)'),
        ),
        array(
            'id'       => 25,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(worry)', ':S', ':-S', ':=S', ':s', ':-s', ':=s'),
        ),
        array(
            'id'       => 26,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(mm)', '(mmm)', '(mmmm)'),
        ),
        array(
            'id'       => 27,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(nerd)', '8-|', 'B-|', '8|', 'B|', '8=|', 'B=|'),
        ),
        array(
            'id'       => 28,
            'name'     => '',
            'title'    => '',
            'replaces' => array(':x', ':-x', ':X', ':-X', ':#', ':-#', ':=x', ':=X', ':=#'),
        ),
        array(
            'id'       => 29,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(hi)', '(wave)', '(bye)'),
        ),
        array(
            'id'       => 30,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(facepalm)', '(fail)'),
        ),
        array(
            'id'       => 31,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(devil)', '(6)'),
        ),
        array(
            'id'       => 32,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(angel)', '(a)', '(A)'),
        ),
        array(
            'id'       => 33,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(envy)', '(v)', '(V)'),
        ),
        array(
            'id'       => 34,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(wait)'),
        ),
        array(
            'id'       => 35,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(bear)', '(hug)'),
        ),
        array(
            'id'       => 36,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(makeup)', '(kate)'),
        ),
        array(
            'id'       => 37,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(giggle)', '(chuckle)'),
        ),
        array(
            'id'       => 38,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(clap)'),
        ),
        array(
            'id'       => 39,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(think)', ':?', ':-?', ':=?'),
        ),
        array(
            'id'       => 40,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(bow)'),
        ),
        array(
            'id'       => 41,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(rofl)', '(rotfl)'),
        ),
        array(
            'id'       => 42,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(whew)'),
        ),
        array(
            'id'       => 43,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(happy)'),
        ),
        array(
            'id'       => 44,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(smirk)'),
        ),
        array(
            'id'       => 45,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(nod)'),
        ),
        array(
            'id'       => 46,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(shake)'),
        ),
        array(
            'id'       => 47,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(waiting)', '(forever)', '(impatience)'),
        ),
        array(
            'id'       => 48,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(emo)'),
        ),
        array(
            'id'       => 49,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(yes)', '(ok)', '(y)', '(Y)'),
        ),
        array(
            'id'       => 50,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(no)', '(n)', '(N)'),
        ),
        array(
            'id'       => 51,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(handshake)'),
        ),
        array(
            'id'       => 52,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(highfive)', '(hifive)', '(h5)'),
        ),
        array(
            'id'       => 53,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(heart)', '(h)', '(H)', '(l)', '(L)'),
        ),
        array(
            'id'       => 54,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(lala)', '(lalala)', '(lalalala)', '(notlistening)'),
        ),
        array(
            'id'       => 55,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(heidy)', '(squirrel)'),
        ),
        array(
            'id'       => 56,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(flower)', '(f)', '(F)'),
        ),
        array(
            'id'       => 57,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(rain)', '(london)', '(st)'),
        ),
        array(
            'id'       => 58,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(sun)', '(#)'),
        ),
        array(
            'id'       => 59,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(tumbleweed)'),
        ),
        array(
            'id'       => 60,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(music)', '(8)'),
        ),
        array(
            'id'       => 61,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(bandit)'),
        ),
        array(
            'id'       => 62,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(tmi)'),
        ),
        array(
            'id'       => 63,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(coffee)', '(c)', '(C)'),
        ),
        array(
            'id'       => 64,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(pizza)', '(pi)'),
        ),
        array(
            'id'       => 65,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(cash)', '(mo)', '($)'),
        ),
        array(
            'id'       => 66,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(muscle)', '(flex)'),
        ),
        array(
            'id'       => 67,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(cake)', '(^)'),
        ),
        array(
            'id'       => 68,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(beer)', '(bricklayers)', '(B)', '(b)'),
        ),
        array(
            'id'       => 69,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(drink)', '(d)', '(D)'),
        ),
        array(
            'id'       => 70,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(dance)', '\\o/', '\\:D/', '\\:d/'),
        ),
        array(
            'id'       => 71,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(ninja)', '(j)', '(J)'),
        ),
        array(
            'id'       => 72,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(star)', '(*)'),
        ),
        array(
            'id'       => 73,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(banghead)', '(headbang)'),
        ),
        array(
            'id'       => 74,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(swear)'),
        ),
        array(
            'id'       => 75,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(drunk)'),
        ),
        array(
            'id'       => 76,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(fubar)'),
        ),
        array(
            'id'       => 77,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(finger)'),
        ),
        array(
            'id'       => 78,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(hrv)', '(poolparty)'),
        ),
        array(
            'id'       => 79,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(rock)'),
        ),
        array(
            'id'       => 80,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(smoking)', '(smoke)', '(ci)'),
        ),
        array(
            'id'       => 81,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(wtf)'),
        ),
        array(
            'id'       => 82,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(bug)'),
        ),
        array(
            'id'       => 83,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(toivo)'),
        ),
        array(
            'id'       => 84,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(wfh)'),
        ),
        array(
            'id'       => 85,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(hollest)'),
        ),
        array(
            'id'       => 86,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(zilmer)'),
        ),
        array(
            'id'       => 87,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(mail)', '(m)', '(e)', '(E)'),
        ),
        array(
            'id'       => 88,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(clock)', '(time)', '(o)', '(O)'),
        ),
        array(
            'id'       => 89,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(film)', '(~)', '(movie)'),
        ),
        array(
            'id'       => 90,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(mp)', '(ph)', '(phone)'),
        ),
        array(
            'id'       => 91,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(talk)'),
        ),
        array(
            'id'       => 92,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(call)', '(t)', '(T)'),
        ),
        array(
            'id'       => 93,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(punch)'),
        ),
        array(
            'id'       => 94,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(u)', '(U)'),
        ),
        array(
            'id'       => 95,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(mooning)'),
        ),
        array(
            'id'       => 96,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(oliver)'),
        ),
        array(
            'id'       => 97,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(football)', '(soccer)', '(so)', '(bartlett)'),
        ),
        // 98 => array(),
        array(
            'id'       => 99,
            'name'     => '',
            'title'    => '',
            'replaces' => array('(skype)', '(ss)'),
        ),
    );

    /*
    public $text3 = array(
        'Afghanistan' => array('(flag:AF)'),
        'Albania' => array('(flag:AL)'),
        'Algeria' => array('(flag:DZ)'),
        'American Samoa' => array('(flag:AS)'),
        'Andorra' => array('(flag:AD)'),
        'Angola' => array('(flag:AO)'),
        'Anguilla' => array('(flag:AI)'),
        'Antarctica' => array('(flag:AQ)'),
        'Antigua and Barbuda' => array('(flag:AG)'),
        'Argentina' => array('(flag:AR)'),
        'Armenia' => array('(flag:AM)'),
        'Aruba' => array('(flag:AW)'),
        'Australia' => array('(flag:AU)'),
        'Austria' => array('(flag:AT)'),
        'Azerbaijan' => array('(flag:AZ)'),
        'Bahamas' => array('(flag:BS)'),
        'Bahrain' => array('(flag:BH)'),
        'Bangladesh' => array('(flag:BD)'),
        'Barbados' => array('(flag:BB)'),
        'Belarus' => array('(flag:BY)'),
        'Belgium' => array('(flag:BE)'),
        'Belize' => array('(flag:BZ)'),
        'Benin' => array('(flag:BJ)'),
        'Bermuda' => array('(flag:BM)'),
        'Bhutan' => array('(flag:BT)'),
        'Bolivia' => array('(flag:BO)'),
        'Bosnia and Herzegovina' => array('(flag:BA)'),
        'Botswana' => array('(flag:BW)'),
        'Brazil' => array('(flag:BR)'),
        'British Indian Ocean Territory' => array('(flag:IO)'),
        'British Virgin Islands' => array('(flag:VG)'),
        'Brunei Darussalam' => array('(flag:BN)'),
        'Bulgaria' => array('(flag:BG)'),
        'Burkina Faso' => array('(flag:BF)'),
        'Burundi' => array('(flag:BI)'),
        'Cambodia' => array('(flag:KH)'),
        'Cameroon' => array('(flag:CM)'),
        'Canada' => array('(flag:CA)'),
        'Cape Verde' => array('(flag:CV)'),
        'Cayman Islands' => array('(flag:KY)'),
        'Central African Republic' => array('(flag:CF)'),
        'Chad' => array('(flag:TD)'),
        'Chile' => array('(flag:CL)'),
        'China' => array('(flag:CN)'),
        'Christmas Island' => array('(flag:CX)'),
        'Cocos Islands' => array('(flag:CC)'),
        'Colombia' => array('(flag:CO)'),
        'Comoros' => array('(flag:KM)'),
        'Congo (DRC)' => array('(flag:CD)'),
        'Congo' => array('(flag:CG)'),
        'Cook Islands' => array('(flag:CK)'),
        'Costa Rica' => array('(flag:CR)'),
        'Cote D&rsquo;Ivoire' => array('(flag:CI)'),
        'Cuba' => array('(flag:CU)'),
        'Cyprus' => array('(flag:CY)'),
        'Czech Republic' => array('(flag:CZ)'),
        'Denmark' => array('(flag:DK)'),
        'Djibouti' => array('(flag:DJ)'),
        'Dominica' => array('(flag:DM)'),
        'Dominican Republic' => array('(flag:DO)'),
        'Ecuador' => array('(flag:EC)'),
        'Egypt' => array('(flag:EG)'),
        'European Union' => array('(flag:EU)'),
        'El Salvador' => array('(flag:SV)'),
        'Equatorial Guinea' => array('(flag:GQ)'),
        'Eritrea' => array('(flag:ER)'),
        'Estonia' => array('(flag:EE)'),
        'Ethiopia' => array('(flag:ET)'),
        'Faroe Islands' => array('(flag:FO)'),
        'Falkland Islands' => array('(flag:FK)'),
        'Fiji' => array('(flag:FJ)'),
        'Finland' => array('(flag:FI)'),
        'France' => array('(flag:FR)'),
        'French Guiana' => array('(flag:GF)'),
        'French Polynesia' => array('(flag:PF)'),
        'French Southern Territories' => array('(flag:TF)'),
        'Gabon' => array('(flag:GA)'),
        'Gambia' => array('(flag:GM)'),
        'Georgia' => array('(flag:GE)'),
        'Germany' => array('(flag:DE)'),
        'Ghana' => array('(flag:GH)'),
        'Gibraltar' => array('(flag:GI)'),
        'Greece' => array('(flag:GR)'),
        'Greenland' => array('(flag:GL)'),
        'Grenada' => array('(flag:GD)'),
        'Guadeloupe' => array('(flag:GP)'),
        'Guam' => array('(flag:GU)'),
        'Guatemala' => array('(flag:GT)'),
        'Guinea' => array('(flag:GN)'),
        'Guinea-Bissau' => array('(flag:GW)'),
        'Guyana' => array('(flag:GY)'),
        'Haiti' => array('(flag:HT)'),
        'Heard and McDonald Islands' => array('(flag:HM)'),
        'Holy See (Vatican City State)' => array('(flag:VA)'),
        'Honduras' => array('(flag:HN)'),
        'Hong Kong' => array('(flag:HK)'),
        'Croatia' => array('(flag:HR)'),
        'Hungary' => array('(flag:HU)'),
        'Iceland' => array('(flag:IS)'),
        'India' => array('(flag:IN)'),
        'Indonesia' => array('(flag:ID)'),
        'Iran' => array('(flag:IR)'),
        'Iraq' => array('(flag:IQ)'),
        'Ireland' => array('(flag:IE)'),
        'Israel' => array('(flag:IL)'),
        'Italy' => array('(flag:IT)'),
        'Jamaica' => array('(flag:JM)'),
        'Japan' => array('(flag:JP)'),
        'Jordan' => array('(flag:JO)'),
        'Kazakhstan' => array('(flag:KZ)'),
        'Kenya' => array('(flag:KE)'),
        'Kiribati' => array('(flag:KI)'),
        'North Korea' => array('(flag:KP)'),
        'Korea' => array('(flag:KR)'),
        'Kuwait' => array('(flag:KW)'),
        'Kyrgyz Republic' => array('(flag:KG)'),
        'Laos' => array('(flag:LA)'),
        'Latvia' => array('(flag:LV)'),
        'Lebanon' => array('(flag:LB)'),
        'Lesotho' => array('(flag:LS)'),
        'Liberia' => array('(flag:LR)'),
        'Libyan Arab Jamahiriya' => array('(flag:LY)'),
        'Liechtenstein' => array('(flag:LI)'),
        'Lithuania' => array('(flag:LT)'),
        'Luxembourg' => array('(flag:LU)'),
        'Macao' => array('(flag:MO)'),
        'Montenegro' => array('(flag:ME)'),
        'Macedonia' => array('(flag:MK)'),
        'Madagascar' => array('(flag:MG)'),
        'Malawi' => array('(flag:MW)'),
        'Malaysia' => array('(flag:MY)'),
        'Maldives' => array('(flag:MV)'),
        'Mali' => array('(flag:ML)'),
        'Malta' => array('(flag:MT)'),
        'Marshall Islands' => array('(flag:MH)'),
        'Martinique' => array('(flag:MQ)'),
        'Mauritania' => array('(flag:MR)'),
        'Mauritius' => array('(flag:MU)'),
        'Mayotte' => array('(flag:YT)'),
        'Mexico' => array('(flag:MX)'),
        'Micronesia' => array('(flag:FM)'),
        'Moldova' => array('(flag:MD)'),
        'Monaco' => array('(flag:MC)'),
        'Mongolia' => array('(flag:MN)'),
        'Montenegro' => array('(flag:ME)'),
        'Montserrat' => array('(flag:MS)'),
        'Morocco' => array('(flag:MA)'),
        'Mozambique' => array('(flag:MZ)'),
        'Myanmar' => array('(flag:MM)'),
        'Namibia' => array('(flag:NA)'),
        'Nauru' => array('(flag:NR)'),
        'Nepal' => array('(flag:NP)'),
        'Netherlands' => array('(flag:NL)'),
        'New Caledonia' => array('(flag:NC)'),
        'New Zealand' => array('(flag:NZ)'),
        'Nicaragua' => array('(flag:NI)'),
        'Niger' => array('(flag:NE)'),
        'Nigeria' => array('(flag:NG)'),
        'Niue' => array('(flag:NU)'),
        'Norfolk Island' => array('(flag:NF)'),
        'Northern Mariana Islands' => array('(flag:MP)'),
        'Norway' => array('(flag:NO)'),
        'Oman' => array('(flag:OM)'),
        'Pakistan' => array('(flag:PK)'),
        'Palau' => array('(flag:PW)'),
        'Palestine' => array('(flag:PS)'),
        'Panama' => array('(flag:PA)'),
        'Papua New Guinea' => array('(flag:PG)'),
        'Paraguay' => array('(flag:PY)'),
        'Peru' => array('(flag:PE)'),
        'Philippines' => array('(flag:PH)'),
        'Pitcairn Island' => array('(flag:PN)'),
        'Poland' => array('(flag:PL)'),
        'Portugal' => array('(flag:PT)'),
        'Puerto Rico' => array('(flag:PR)'),
        'Qatar' => array('(flag:QA)'),
        'Reunion' => array('(flag:RE)'),
        'Romania' => array('(flag:RO)'),
        'Russian Federation' => array('(flag:RU)'),
        'Rwanda' => array('(flag:RW)'),
        'Serbia' => array('(flag:RS)'),
        'South Sudan' => array('(flag:SS)'),
        'Samoa' => array('(flag:WS)'),
        'San Marino' => array('(flag:SM)'),
        'Sao Tome and Principe' => array('(flag:ST)'),
        'Saudi Arabia' => array('(flag:SA)'),
        'Senegal' => array('(flag:SN)'),
        'Serbia' => array('(flag:RS)'),
        'Seychelles' => array('(flag:SC)'),
        'Sierra Leone' => array('(flag:SL)'),
        'Singapore' => array('(flag:SG)'),
        'Slovakia' => array('(flag:SK)'),
        'Slovenia' => array('(flag:SI)'),
        'Solomon Islands' => array('(flag:SB)'),
        'Somalia' => array('(flag:SO)'),
        'South Africa' => array('(flag:ZA)'),
        'Spain' => array('(flag:ES)'),
        'Sri Lanka' => array('(flag:LK)'),
        'St. Helena' => array('(flag:SH)'),
        'St. Kitts and Nevis' => array('(flag:KN)'),
        'St. Lucia' => array('(flag:LC)'),
        'St. Pierre and Miquelon' => array('(flag:PM)'),
        'St. Vincent and the Grenadines' => array('(flag:VC)'),
        'Sudan' => array('(flag:SD)'),
        'Suriname' => array('(flag:SR)'),
        'Swaziland' => array('(flag:SZ)'),
        'Sweden' => array('(flag:SE)'),
        'Switzerland' => array('(flag:CH)'),
        'Syria' => array('(flag:SY)'),
        'Taiwan' => array('(flag:TW)'),
        'Tajikistan' => array('(flag:TJ)'),
        'Tanzania' => array('(flag:TZ)'),
        'Thailand' => array('(flag:TH)'),
        'Timor-Leste' => array('(flag:TL)'),
        'Togo' => array('(flag:TG)'),
        'Tokelau' => array('(flag:TK)'),
        'Tonga' => array('(flag:TO)'),
        'Trinidad and Tobago' => array('(flag:TT)'),
        'Tunisia' => array('(flag:TN)'),
        'Turkey' => array('(flag:TR)'),
        'Turkmenistan' => array('(flag:TM)'),
        'Turks and Caicos Islands' => array('(flag:TC)'),
        'Tuvalu' => array('(flag:TV)'),
        'US Virgin Islands' => array('(flag:VI)'),
        'Uganda' => array('(flag:UG)'),
        'Ukraine' => array('(flag:UA)'),
        'United Arab Emirates' => array('(flag:AE)'),
        'United Kingdom' => array('(flag:GB)'),
        'United States of America' => array('(flag:US)'),
        'Uruguay' => array('(flag:UY)'),
        'Uzbekistan' => array('(flag:UZ)'),
        'Vanuatu' => array('(flag:VU)'),
        'Venezuela' => array('(flag:VE)'),
        'Viet Nam' => array('(flag:VN)'),
        'Wallis and Futuna Islands' => array('(flag:WF)'),
        'Yemen' => array('(flag:YE)'),
        'Zambia' => array('(flag:ZM)'),
        'Zimbabwe' => array('(flag:ZW)'),
    );*/
}