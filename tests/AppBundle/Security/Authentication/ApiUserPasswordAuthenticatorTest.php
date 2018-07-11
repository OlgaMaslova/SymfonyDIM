<?php

namespace Tests\AppBundle\Security\Authentication;

use AppBundle\Entity\User;
use AppBundle\Security\Authentication\ApiUserPasswordAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;


class ApiUserPasswordAuthenticatorTest extends TestCase
{
    public function testGetCredentialsWithoutUsernameInRequest()
    {
        $encoderFactory = new EncoderFactory([]);

        $request = new Request();
        $request->headers->add(['X-PASSWORD' => 'mypswd']);

        $authenticator = new ApiUserPasswordAuthenticator($encoderFactory);
        $result =  $authenticator->getCredentials($request);

        $this->assertNull($result);
    }

    public function testGetCredentialsWithoutPasswordInRequest()
    {
        $encoderFactory = new EncoderFactory([]);

        $request = new Request();
        $request->headers->add(['X-USERNAME' => 'username']);

        $authenticator = new ApiUserPasswordAuthenticator($encoderFactory);
        $result =  $authenticator->getCredentials($request);

        $this->assertNull($result);
    }

    public function testGetCredentialsWithoutAnyHeaders()
    {
        $encoderFactory = new EncoderFactory([]);

        $request = new Request();

        $authenticator = new ApiUserPasswordAuthenticator($encoderFactory);
        $result =  $authenticator->getCredentials($request);

        $this->assertNull($result);
    }

    public function testGetCredentialsFromHeaders()
    {
        $encoderFactory = new EncoderFactory([]);

        $request = new Request();
        $request->headers->add(['X-USERNAME' => 'Me']);
        $request->headers->add(['X-PASSWORD' => 'mypswd']);

        $authenticator = new ApiUserPasswordAuthenticator($encoderFactory);
        $result =  $authenticator->getCredentials($request);

        $expected = [];
        $expected['username'] = 'Me';
        $expected['password'] = 'mypswd';

        $this->assertSame($expected, $result);
    }


    /**
     * @dataProvider getHeaders
     */

    public function getHeaders()
    {
        return [
            [['X-USERNAME' => 'ME']],
            [['X-PASSWORD' => 'mypswd']]
        ];
    }

    public function testCheckCredentialsAreCorrect()
    {
        $encoder = new BCryptPasswordEncoder(13);

        $encoderFactory = $this
            ->getMockBuilder(EncoderFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $encoderFactory->method('getEncoder')->willReturn($encoder);

        $authenticator = new ApiUserPasswordAuthenticator($encoderFactory);

        $user = new User();
        $user -> setPassword('$2y$13$HY31GBJxFfYSMrNL9BF0yeEb.t/tFuMh02RfYlbmKLT4JTXfW96fa');
        $credentials = ['username' => 'ME', 'password' => 'mypwd'];


        $result = $authenticator->checkCredentials($credentials, $user);

        $this->assertSame(true, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AuthenticationException
     *
     */
    public function testCheckCredentialsAreWrong()
    {
        $encoder = new BCryptPasswordEncoder(13);

        $encoderFactory = $this
            ->getMockBuilder(EncoderFactoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $encoderFactory->method('getEncoder')->willReturn($encoder);

        $authenticator = new ApiUserPasswordAuthenticator($encoderFactory);

        $user = new User();
        $user -> setPassword('wrongPassword');
        $credentials = ['username' => 'ME', 'password' => 'mypwd'];


        $result = $authenticator->checkCredentials($credentials, $user);

    }
}