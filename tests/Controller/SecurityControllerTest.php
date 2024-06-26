<?php

namespace App\Tests\Controller;

use App\Tests\Controller\AbstractControllerTest;

class SecurityControllerTest extends AbstractControllerTest
{
    public function testLogin(): void
    {
        $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testLogout(): void
    {
        $this->authenticateUser();
        $this->client->request('GET', '/logout');
        $this->throwException(new \Exception('Logout'));
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}