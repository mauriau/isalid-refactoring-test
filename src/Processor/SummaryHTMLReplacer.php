<?php

namespace App\Processor;

use App\Entity\Quote;

class SummaryHTMLReplacer implements Replacer
{
    public function replace(string $subject, $replace): string
    {
        $containsSummaryHtml = false !== strpos($subject, '[quote:summary_html]');
        if (!$containsSummaryHtml) {
            return $subject;
        }

        return str_replace(
            '[quote:summary_html]',
            Quote::renderHtml($replace),
            $subject
        );
    }
}