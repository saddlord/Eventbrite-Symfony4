<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT', 'DELETE'])
            && $subject instanceof \App\Entity\Event;
    }


    protected function voteOnAttribute($attribute, $event, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        if(null == $event->getAuthor()) {
            return false; }


        switch ($attribute) {
            case 'EDIT':
                return $event->getAuthor()->getId() == $user->getId();
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'DELETE':
                return $event->getAuthor()->getId() == $user->getId();
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
