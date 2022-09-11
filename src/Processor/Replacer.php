<?php

namespace App\Processor;

interface Replacer
{
    /** @var $replace mixed Quote|User */
    public function replace(string $subject, $replace): string;
}
