<?php
namespace AppBundle\Security\Authorization;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if ($attribute != self::ROLE_ADMIN) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Get the User => $user
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        // If the user has ROLE_ADMIN, he can do stuff.
        if ('ROLE_ADMIN' === $attribute && in_array(self::ROLE_ADMIN, $user->getRoles())) {
            return true;
        }

        // Otherwise, the user does not have the right.
        // return false
        return false;
    }

}