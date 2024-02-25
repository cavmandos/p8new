<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function Symfony\Component\Clock\now;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $task = new Task();
            $task->setCreatedAt(now());
            $task->setTitle('Titre de la tâche '.$i.'');
            $task->setContent('Il faut faire ça');
            $task->setIsDone(true);
            $manager->persist($task);
        }

        for ($i = 5; $i < 10; $i++) {
            $task = new Task();
            $task->setCreatedAt(now());
            $task->setTitle('Titre de la tâche '.$i.'');
            $task->setContent('Il faut faire ceci');
            $task->setIsDone(false);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
