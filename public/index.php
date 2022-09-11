<?php

use App\Entity\Template;
use App\Entity\Quote;
use App\TemplateManager;

require __DIR__.'/../vendor/autoload.php';

$faker = \Faker\Factory::create();

$template = new Template(
    1,
    'Votre livraison à [quote:destination_name]',
    "
Bonjour [user:first_name],

Merci de nous avoir contacté pour votre livraison à [quote:destination_name].

Bien cordialement,

L'équipe de Shipper
");
$templateManager = new TemplateManager();

$message = $templateManager->getTemplateComputed(
    $template,
    ['quote' => new Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), new \DateTime($faker->date()))]
);

echo $message->getSubject().PHP_EOL.$message->getContent();
