<?php

namespace League\CLImate\TerminalObject;

class Br extends Repeatable
{
    /**
     * Return an empty string
     *
     * @return string
     */

    public function result()
    {
        return array_fill(0, $this->count, '');
    }
}
