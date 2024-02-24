<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testIndexRequest(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    public function testTaskListRequest(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }
}
