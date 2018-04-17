<?php
namespace AppBundle\Security\Authorization;

use AppBundle\Entity\Show;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ShowVoter extends Voter
{


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
        if ($subject instanceof Show) {
            return true;
        }

        return false;
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
    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Get the User => $user
        $user = $token->getUser();

        // Get the Show => $show
        $show = $subject;

        // If the user is the author of this show, he can do stuff.
        // If $show->getAuthor === $user ====> return true
        if ($show->getAuthor() === $user) {
            return true;
        }

        // Otherwise, the user does not have the right.
        // return false
        return false;
    }
}