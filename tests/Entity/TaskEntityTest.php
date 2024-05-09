<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    private $task;

    private $date;

    public function setUp(): void
    {
        $this->task = new Task();
        $this->date = new \DateTime();
    }

    public function testCreatedAt(): void
    {
        $this->task->setCreatedAt($this->date);
        $this->assertSame($this->date, $this->task->getCreatedAt());
    }

    public function testId(): void
    {
        $this->assertNull($this->task->getId());
    }

    public function testTitle(): void
    {
        $this->task->setTitle('Test de titre');
        $this->assertSame($this->task->getTitle(), 'Test de titre');
    }

    public function testContent(): void
    {
        $this->task->setContent('Test de contenu');
        $this->assertSame($this->task->getContent(), 'Test de contenu');
    }

    public function testToggle(): void
    {
        $this->task->toggle(true);
        $this->assertEquals($this->task->isIsDone(), true);
    }

    public function testIsDone(): void
    {
        $this->task->setIsDone(true);
        $this->assertEquals($this->task->isIsDone(), true);
    }

    public function testUser(): void
    {
        $this->task->setUserId(new User());
        $this->assertInstanceOf(User::class, $this->task->getUserId());
    }
}