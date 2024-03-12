<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

abstract class AbstractControllerTest extends WebTestCase
{
    public $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function authenticateAdmin(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy(['email' => 'admin@mail.com']);
        $this->client->loginUser($getUser);
    }

    public function authenticateUser(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy(['email' => 'user-one@mail.com']);
        $this->client->loginUser($getUser);
    }

}
