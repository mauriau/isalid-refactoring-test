<?php


namespace App\Processor;


use App\Context\ApplicationContext;
use App\Entity\Quote;
use App\Entity\User;
use App\Repository\DestinationRepository;
use App\Repository\SiteRepository;

class DestinationReplacer implements Replacer
{
    public function replace(string $subject, $replace): string
    {
        $containDestination = false !== strpos($subject, '[quote:destination_name]');
        $containDestinationLink = false !== strpos($subject, '[quote:destination_link]');
        if (!$containDestination && !$containDestinationLink) {
            return $subject;
        }

        $destination = DestinationRepository::getInstance()->getById($replace->getDestinationId());
        if ($containDestination) {
            $subject = str_replace('[quote:destination_name]', $destination->getCountryName(), $subject);
        }

        if ($containDestinationLink) {
            $site = SiteRepository::getInstance()->getById($replace->getSiteId());
            $subject = str_replace('[quote:destination_link]', $site->getUrl().'/'.$destination->getCountryName().'/quote/'.$replace->getId(), $subject);
        }

        return $subject;
    }
}