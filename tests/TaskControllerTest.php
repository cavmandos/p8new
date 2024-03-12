<?php

namespace App\Tests;

use App\Repository\TaskRepository;

class TaskControllerTest extends AbstractControllerTest
{
    private function getTaskId(): int
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy([], ['id' => 'desc']);
        return $task->getId();
    }

    public function testIfNotLogged(): void
    {
        $this->client->request('GET', '/tasks');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testIfLogged(): void
    {
        $this->authenticateUser();
        $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskIfNotLogged(): void
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskIfLogged(): void
    {
        $this->authenticateUser();
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Créer la tâche')->form();
        $form['task[title]'] = "Test d'un titre";
        $form['task[content]'] = 'Test du contenu';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testToggleIfNotLogged(): void
    {
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy([], ['id' => 'desc']);
        $id = $task->getId();
        $this->client->request('GET', "/tasks/$id/toggle");
        $this->assertEquals(false, $task->isIsDone());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testToggleIfLogged(): void
    {
        $this->authenticateUser();
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $task = $taskRepository->findOneBy([], ['id' => 'desc']);
        $id = $task->getId();
        $this->client->request('GET', "/tasks/$id/toggle");
        $this->assertEquals(true, $task->isIsDone());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateIfNotLogged(): void
    {
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/edit");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateIfLogged(): void
    {
        $this->authenticateUser();
        $id = $this->getTaskId();
        $crawler = $this->client->request('GET', "/tasks/$id/edit");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier la tâche')->form();
        $form['task[title]'] = "Test d'un titre modifié";
        $form['task[content]'] = 'Test du contenu modifié';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteIfNotLogged(): void
    {
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskIfLoggedButNotOwner(): void
    {
        $this->authenticateAdmin();
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskIfOwner(): void
    {
        $this->authenticateUser();
        $id = $this->getTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $this->client->followRedirect()->filter('div.alert-success')->count());
    }

}
