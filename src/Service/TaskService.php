<?php

namespace App\Service;

use App\Entity\Task;
use Symfony\Bundle\SecurityBundle\Security;

class TaskService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prepareTask(Task $task)
    {
        $now = new \DateTime();
        $task->setCreatedAt($now);
        $user = $this->security->getUser();
        $task->setUserId($user);
        $task->setIsDone(false);

        return $task;
    }
}