<?php

namespace App;

use App\Context\ApplicationContext;
use App\Entity\Template;
use App\Entity\User;
use App\Quote\Quote;
use App\Repository\DestinationRepository;
use App\Repository\QuoteRepository;
use App\Repository\SiteRepository;

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data): Template
    {
        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText(string $text, array $data): string
    {
        $APPLICATION_CONTEXT = ApplicationContext::getInstance();

        $quote = (isset($data['quote']) and $data['quote'] instanceof Quote) ? $data['quote'] : null;

        if ($quote) {
            $quote = QuoteRepository::getInstance()->getById($quote->getId());
            $site = SiteRepository::getInstance()->getById($quote->getSiteId());
            $destinationOfQuote = DestinationRepository::getInstance()->getById($quote->getDestinationId());

            if (strpos($text, '[quote:destination_link]') !== false) {
                $destination = DestinationRepository::getInstance()->getById($quote->getDestinationId());
            }

            $containsSummaryHtml = strpos($text, '[quote:summary_html]');
            $containsSummary = strpos($text, '[quote:summary]');

            if ($containsSummaryHtml !== false || $containsSummary !== false) {
                if ($containsSummaryHtml !== false) {
                    $text = str_replace(
                        '[quote:summary_html]',
                        Quote::renderHtml($quote),
                        $text
                    );
                }
                if ($containsSummary !== false) {
                    $text = str_replace(
                        '[quote:summary]',
                        Quote::renderText($quote),
                        $text
                    );
                }
            }

            (strpos($text, '[quote:destination_name]') !== false) and $text = str_replace('[quote:destination_name]', $destinationOfQuote->countryName, $text);
        }

        if (isset($destination)) {
            $text = str_replace('[quote:destination_link]', $site->getUrl().'/'.$destination->getCountryName().'/quote/'.$quote->id, $text);
        } else {
            $text = str_replace('[quote:destination_link]', '', $text);
        }

        $user = (isset($data['user']) and ($data['user'] instanceof User)) ? $data['user'] : $APPLICATION_CONTEXT->getCurrentUser();
        if ($user) {
            (strpos($text, '[user:first_name]') !== false) and $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($user->firstname)), $text);
        }

        return $text;
    }
}
