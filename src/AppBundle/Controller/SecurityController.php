<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route(name="security_")
 */

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authUtils)
    {

        return $this->render('security/login.html.twig', [
            'error'=>$authUtils->getLastAuthenticationError(),
            'lastUsername'=>$authUtils->getLastUsername()
        ]);
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheck()
    {
        dump('This code is never executed');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        dump('This code is never executed');
    }


}