<?php


namespace App\Processor;


use App\Context\ApplicationContext;
use App\Entity\User;

class UserReplacer implements Replacer
{
    public function replace(string $subject, $replace): string
    {
        $containFirstname = false !== strpos($subject, '[user:first_name]');
        if (!$containFirstname) {
            return $subject;
        }
        $user = $this->getCurrentUser($subject);

        return str_replace('[user:first_name]', $user->getFirstname(), $subject);
    }

    private function getCurrentUser($user): User
    {
        $context = ApplicationContext::getInstance();

        return $user instanceof User ? $user : $context->getCurrentUser();
    }
}