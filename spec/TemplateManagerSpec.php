<?php

namespace spec\App;

use App\Entity\Destination;
use App\Entity\Site;
use App\Entity\Template;
use App\Quote\Quote;
use App\Repository\DestinationRepository;
use App\Repository\SiteRepository;
use App\TemplateManager;
use PhpSpec\ObjectBehavior;

class TemplateManagerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TemplateManager::class);
    }

    public function it_compute_template(Template $template, Quote $quote, Template $templateComputed, Destination $destination, Site $site)
    {
        $template->getId()->willReturn(1);
        $template->getSubject()->willReturn('Votre livraison à [quote:destination_name]');
        $template->getContent()->willReturn(
            'Bonjour [user:first_name],
            Merci de nous avoir contacté pour votre livraison à [quote:destination_name].
            Bien cordialement,
            L\'équipe de Shipper'
        );

        $quote->getId()->willReturn(1);
        $quote->getDestinationId()->willReturn(2);
        $quote->getDateQuoted()->willReturn(new \DateTime());
        $quote->getSiteId()->willReturn(3);

        $templateComputed->getId()->willReturn(1);
        $templateComputed->getSubject()->willReturn('Votre livraison à la maison');
        $templateComputed->getSubject()->willReturn(
            'Bonjour Maxime,
            Merci de nous avoir contacté pour votre livraison à la maison.
            Bien cordialement,
            L\'équipe de Shipper'
        );
        $destination->getId()->willReturn(2);
        $destination->getCountryName()->willReturn('la maison');
        DestinationRepository::getInstance()->getById(2)->shouldBeCalled()->willReturn($destination);

        $site->getId()->willReturn(3);
        $site->getUrl()->willReturn('https://localhost');
        SiteRepository::getInstance()->getById(3)->shouldBeCalled()->willReturn($site);

        $this->getTemplateComputed($template, ['quote' => $quote])->shouldReturn($templateComputed);
    }

//    public function it_should_test_get_user();
//    public function it_should_replace_subject();
//    public function it_should_replace_destination();
//    public function it_should_replace_destination();
}