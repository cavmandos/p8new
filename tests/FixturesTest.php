<?php

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixturesTest extends TestCase
{
    public function testLoad(): void
    {
        $userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $userPasswordHasher->method('hashPassword')->willReturn('hashed_password');

        $manager = $this->createMock(ObjectManager::class);
        // 24 tasks + 1 admin + 2 users = 27
        $manager->expects($this->exactly(27))->method('persist');
        $manager->expects($this->once())->method('flush');

        $fixtures = new AppFixtures($userPasswordHasher);
        $fixtures->load($manager);
    }
}