<?php

namespace Wheels\Typo\Module\Emoticon\Emoji;

use Wheels\Typo\Module\Emoticon\Emoticon;

/**
 * Эмотиконы (смайлики) Emoji.
 *
 * @link http://www.emoji-cheat-sheet.com/
 */
class Emoji extends Emoticon
{
    /**
     * {@inheritDoc}
     */
    static public $smiles = array(
        array(
            'id'       => 1,
            'name'     => 'bowtie',
            'title'    => '',
            'replaces' => array(':bowtie:'),
        ),
        array(
            'id'       => 2,
            'name'     => 'relaxed',
            'title'    => '',
            'replaces' => array(':relaxed:'),
        ),
        array(
            'id'       => 3,
            'name'     => 'flushed',
            'title'    => '',
            'replaces' => array(':flushed:'),
        ),
        array(
            'id'       => 4,
            'name'     => 'stuck_out_tongue_winking_eye',
            'title'    => '',
            'replaces' => array(':stuck_out_tongue_winking_eye:'),
        ),
        array(
            'id'       => 5,
            'name'     => 'stuck_out_tongue',
            'title'    => '',
            'replaces' => array(':stuck_out_tongue:'),
        ),
        array(
            'id'       => 6,
            'name'     => 'open_mouth',
            'title'    => '',
            'replaces' => array(':open_mouth:'),
        ),
        array(
            'id'       => 7,
            'name'     => 'unamused',
            'title'    => '',
            'replaces' => array(':unamused:'),
        ),
        array(
            'id'       => 8,
            'name'     => 'pensive',
            'title'    => '',
            'replaces' => array(':pensive:'),
        ),
        array(
            'id'       => 9,
            'name'     => 'persevere',
            'title'    => '',
            'replaces' => array(':persevere:'),
        ),
        array(
            'id'       => 10,
            'name'     => 'scream',
            'title'    => '',
            'replaces' => array(':scream:'),
        ),
    );
}