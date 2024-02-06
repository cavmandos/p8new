<?php

use PHPUnit\Framework\TestCase;
use App\Controller\DefaultController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends TestCase
{
    public function testIndexReturnsResponse()
    {
        $container = $this->createMock(ContainerInterface::class);
        $controller = new DefaultController();
        $controller->setContainer($container);
        $response = $controller->index();
        $this->assertInstanceOf(Response::class, $response);
    }
}
