<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Template;
use App\Entity\User;
use App\Quote\Quote;
use App\Repository\DestinationRepository;
use App\Repository\SiteRepository;

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, Quote $quote): Template
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $quote);
        $replaced->content = $this->computeText($replaced->content, $quote);

        return $replaced;
    }

    private function computeText(string $text, Quote $quote, ?User $user = null): string
    {
        return $this->computeQuote($quote, $text, $user);
    }

    public function computeQuote(Quote $quote, string $text, ?User $user): string
    {
        $text = $this->computeSummaryHTML($text, $quote);
        $text = $this->computeSummary($text, $quote);
        $text = $this->computeDestination($text, $quote);

        return $this->computeUser($text, $user);
    }

    private function getCurrentUser(?User $user): User
    {
        $context = ApplicationContext::getInstance();

        return $user instanceof User ? $user : $context->getCurrentUser();
    }

    private function computeUser(string $text, User $user): string
    {
        $containFirstname = false !== strpos($text, '[user:first_name]');
        if (!$containFirstname) {
            return $text;
        }
        $user = $this->getCurrentUser($user);

        return str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
    }

    private function computeDestination(string $text, Quote $quote): string
    {
        $containDestination = false !== strpos($text, '[quote:destination_name]');
        if (!$containDestination) {
            return $text;
        }

        $destination = DestinationRepository::getInstance()->getById($quote->getDestinationId());
        $site = SiteRepository::getInstance()->getById($quote->getSiteId());
        $text = str_replace('[quote:destination_name]', $destination->countryName, $text);

        return str_replace('[quote:destination_link]', $site->getUrl().'/'.$destination->getCountryName().'/quote/'.$quote->id, $text);
    }

    private function computeSummaryHTML(string $text, Quote $quote): string
    {
        $containsSummaryHtml = false !== strpos($text, '[quote:summary_html]');
        if (!$containsSummaryHtml) {
            return $text;
        }

        return str_replace(
            '[quote:summary_html]',
            Quote::renderText($quote),
            $text
        );

    }

    private function computeSummary(string $text, Quote $quote): string
    {
        $containsSummary = false !== strpos($text, '[quote:summary]');
        if (!$containsSummary) {
            return $text;
        }

        return str_replace(
            '[quote:summary]',
            Quote::renderText($quote),
            $text
        );
    }
}
