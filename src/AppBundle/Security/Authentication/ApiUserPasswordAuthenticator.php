<?php

namespace AppBundle\Security\Authentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiUserPasswordAuthenticator extends AbstractGuardAuthenticator
{
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * @param Request $request The request that resulted in an AuthenticationException
     * @param AuthenticationException $authException The exception that started the authentication process
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new JsonResponse('The authentication is required', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g. an associate array).
     *
     * @param Request $request
     * @return mixed Any non-null value
     * @throws \UnexpectedValueException If null is returned
     */
    public function getCredentials(Request $request)
    {
        if (!$request->headers->has('X_USERNAME') || !$request->headers->has('X_PASSWORD')) {
            return null;
        }

        $credentials = [];
        $credentials['username'] = $request->headers->get('X_USERNAME');
        $credentials['password'] = $request->headers->get('X_PASSWORD');

        return $credentials;
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @throws AuthenticationException
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $userProvider->loadUserByUsername($credentials['username']);
        return $user;
    }

    /**
     * Returns true if the credentials are valid.
     *
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {

        $encoder = $this->encoderFactory->getEncoder($user);

        //dump($hashedPassword, $user->getPassword());die();
        if ($encoder->isPasswordValid($user->getPassword(), $credentials['password'], null)) {
            return true;
        }

        throw new AuthenticationException("Authentication failed");
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse('Authentication failed.', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication executed and was successful!
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * Does this method support remember me cookies?
     *
     * Remember me cookie will be set if *all* of the following are met:
     *  A) This method returns true
     *  B) The remember_me key under your firewall is configured
     *  C) The "remember me" functionality is activated. This is usually
     *      done by having a _remember_me checkbox in your form, but
     *      can be configured by the "always_remember_me" and "remember_me_parameter"
     *      parameters under the "remember_me" firewall key
     *  D) The onAuthenticationSuccess method returns a Response object
     *
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}