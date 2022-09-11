<?php


namespace App\Processor;


use App\Entity\Quote;

class SummaryReplacer implements Replacer
{
    public function replace(string $subject, $replace): string
    {
        $containsSummary = false !== strpos($subject, '[quote:summary]');
        if (!$containsSummary) {
            return $subject;
        }

        return str_replace(
            '[quote:summary]',
            Quote::renderText($replace),
            $subject
        );
    }
}