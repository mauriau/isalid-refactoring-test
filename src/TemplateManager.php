<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Template;
use App\Entity\User;
use App\Entity\Quote;
use App\Repository\DestinationRepository;
use App\Repository\SiteRepository;

class TemplateManager
{
    private $quote;
    private $user;

    public function getTemplateComputed(Template $tpl, array $data)
    {
        $clonedTemplate = clone($tpl);
        $quote = (isset($data['quote']) && $data['quote'] instanceof Quote) ? $data['quote'] : null;
        $user = (isset($data['user']) && $data['user'] instanceof User) ? $data['user'] : null;
        if (!$quote instanceof Quote) {
            return $clonedTemplate;
        }
        $this->quote = $quote;
        $this->user = $user;

        $this->computeSubject($clonedTemplate);
        $this->computeContent($clonedTemplate);

        return $clonedTemplate;
    }

    private function computeSubject(Template $template): void
    {
        $subject = $template->getSubject();
        $subject = $this->computeSummaryHTML($subject);
        $subject = $this->computeSummary($subject);
        $subject = $this->computeDestination($subject);
        $subject = $this->computeUser($subject);

        $template->setSubject($subject);
    }

    private function computeContent(Template $template): void
    {
        $content = $template->getContent();
        $content = $this->computeSummaryHTML($content);
        $content = $this->computeSummary($content);
        $content = $this->computeDestination($content);
        $content = $this->computeUser($content);

        $template->setContent($content);
    }

    private function getCurrentUser(): User
    {
        $context = ApplicationContext::getInstance();

        return $this->user instanceof User ? $this->user : $context->getCurrentUser();
    }

    private function computeUser(string $text): string
    {
        $containFirstname = false !== strpos($text, '[user:first_name]');
        if (!$containFirstname) {
            return $text;
        }
        $user = $this->getCurrentUser();

        return str_replace('[user:first_name]', $user->getFirstname(), $text);
    }

    private function computeDestination(string $text): string
    {
        $containDestination = false !== strpos($text, '[quote:destination_name]');
        $containDestinationLink = false !== strpos($text, '[quote:destination_link]');
        if (!$containDestination && !$containDestinationLink) {
            return $text;
        }

        $destination = DestinationRepository::getInstance()->getById($this->quote->getDestinationId());
        if ($containDestination) {
            $text = str_replace('[quote:destination_name]', $destination->getCountryName(), $text);
        }

        if ($containDestinationLink) {
            $site = SiteRepository::getInstance()->getById($this->quote->getSiteId());
            $text = str_replace('[quote:destination_link]', $site->getUrl().'/'.$destination->getCountryName().'/quote/'.$this->quote->getId(), $text);
        }

        return $text;
    }

    private function computeSummaryHTML(string $text): string
    {
        $containsSummaryHtml = false !== strpos($text, '[quote:summary_html]');
        if (!$containsSummaryHtml) {
            return $text;
        }

        return str_replace(
            '[quote:summary_html]',
            Quote::renderHtml($this->quote),
            $text
        );
    }

    private function computeSummary(string $text): string
    {
        $containsSummary = false !== strpos($text, '[quote:summary]');
        if (!$containsSummary) {
            return $text;
        }

        return str_replace(
            '[quote:summary]',
            Quote::renderText($this->quote),
            $text
        );
    }
}
