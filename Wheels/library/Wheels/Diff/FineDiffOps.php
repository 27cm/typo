<?php

namespace Wheels\Diff;

use Wheels\Diff\Opcode\Copy;
use Wheels\Diff\Opcode\Delete;
use Wheels\Diff\Opcode\Insert;

/**
 * FineDiff ops
 *
 * Collection of ops
 */
class FineDiffOps
{
    public $edits = array();

    public function appendOpcode($opcode, $from, $from_offset, $from_len)
    {
        switch($opcode)
        {
            case 'c' :
                $edits[] = new Copy($from_len);
            break;
            case 'd' :
                $edits[] = new Delete($from_len);
            break;
            case 'i' :
                $edits[] = new Insert(substr($from, $from_offset, $from_len));
            break;
        }
    }
}
