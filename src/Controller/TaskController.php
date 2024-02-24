<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task_list')]
    public function listAction(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();
        return $this->render('task/list.html.twig', [
            'tasks' => $tasks
        ]);
    }

    #[Route('/tasks/create', name: 'app_task_create')]
    public function createAction(): Response
    {
        return $this->render('task/create.html.twig');
    }

    #[Route('/tasks/{}/edit', name: 'app_task_edit')]
    public function editAction(): Response
    {
        return $this->render('task/edit.html.twig');
    }

    #[Route('/tasks/{}/toggle', name: 'app_task_toggle')]
    public function toggleTaskAction(): Response
    {
        return dd("Toggle");
    }

    #[Route('/tasks/{}/delete', name: 'app_task_delete')]
    public function deleteTaskAction(): Response
    {
        return dd("Delete");
    }
}
