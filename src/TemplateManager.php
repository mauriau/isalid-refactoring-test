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
    public function getTemplateComputed(Template $tpl, array $data)
    {
        $replaced = clone($tpl);
        $quote = (isset($data['quote']) && $data['quote'] instanceof Quote) ? $data['quote'] : null;
        if (!$quote instanceof Quote) {
            return $replaced;
        }
        $user = (isset($data['user']) && $data['user'] instanceof User) ? $data['user'] : null;

        $replaced->setSubject($this->computeText($replaced->getSubject(), $quote, $user));
        $replaced->setContent($this->computeText($replaced->getContent(), $quote, $user));

        return $replaced;
    }

    private function computeText(string $text, Quote $quote, ?User $user = null): string
    {
        return $this->computeQuote($quote, $text, $user);
    }

    private function computeQuote(Quote $quote, string $text, ?User $user): string
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

        return str_replace('[user:first_name]', ucfirst(mb_strtolower($user->getFirstname())), $text);
    }

    private function computeDestination(string $text, Quote $quote): string
    {
        $containDestination = false !== strpos($text, '[quote:destination_name]');
        $containDestinationLink = false !== strpos($text, '[quote:destination_link]');
        if (!$containDestination && !$containDestinationLink) {
            return $text;
        }

        $destination = DestinationRepository::getInstance()->getById($quote->getDestinationId());
        $text = str_replace('[quote:destination_name]', $destination->getCountryName(), $text);

        $site = SiteRepository::getInstance()->getById($quote->getSiteId());

        return str_replace('[quote:destination_link]', $site->getUrl().'/'.$destination->getCountryName().'/quote/'.$quote->getId(), $text);
    }

    private function computeSummaryHTML(string $text, Quote $quote): string
    {
        $containsSummaryHtml = false !== strpos($text, '[quote:summary_html]');
        if (!$containsSummaryHtml) {
            return $text;
        }

        return str_replace(
            '[quote:summary_html]',
            Quote::renderHtml($quote),
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
