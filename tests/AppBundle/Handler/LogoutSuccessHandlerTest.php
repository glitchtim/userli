<?php

namespace Tests\AppBundle\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use AppBundle\Handler\LogoutSuccessHandler;

class LogoutSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogout()
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $session = new Session(new MockArraySessionStorage());

        $request->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($session));

        $response = new Response();
        $httpUtils = $this->getMockBuilder('Symfony\Component\Security\Http\HttpUtils')->getMock();
        $httpUtils->expects($this->once())
            ->method('createRedirectResponse')
            ->with($request, '/')
            ->will($this->returnValue($response));

        $handler = new LogoutSuccessHandler($httpUtils, '/');
        $result = $handler->onLogoutSuccess($request);

        $this->assertSame($response, $result);
        $this->assertArrayHasKey('success', $request->getSession()->getFlashBag()->all());
    }
}
