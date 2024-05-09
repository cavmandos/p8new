<?php

namespace App\Controller;

use Exception;
use App\Entity\Task;
use App\Form\TaskType;
use App\Service\TaskService;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task_list')]
    public function listAction(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findBy(['userId' => $this->getUser()], ['createdAt' => 'DESC']);
        $anonymous = $taskRepository->findBy(['userId' => null], ['createdAt' => 'DESC']);
        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'anonymous' => $anonymous
        ]);
    }

    #[Route('/tasks/create', name: 'app_task_create')]
    public function createAction(Request $request, EntityManagerInterface $entityManager, TaskService $taskService): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $taskService->prepareTask($task);
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash('success', "La tâche a bien été créée");
            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'app_task_edit')]
    public function editAction(Request $request, Task $task, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($task->getUserId() == null || $task->getUserId() != $user) {
            $this->addFlash('error', "Cette tâche n'est pas modifiable");
            return $this->redirectToRoute('app_task_list');
        } elseif ($form->isSubmitted()) {
            $entityManager->flush();
            $this->addFlash('success', "La tâche a bien été mise à jour");
            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'app_task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $entityManager): Response
    {
        $task->toggle(!$task->isIsDone());
        $entityManager->flush();
        $this->addFlash('success', 'La tâche a bien changé de statut');
        return $this->redirectToRoute('app_task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'app_task_delete')]
    public function deleteTaskAction(Task $task, EntityManagerInterface $entityManager, Security $security): Response
    {
        $admin = $security->getUser()->getRoles();

        if ($task->getUserId() == null && $admin[0] == "ROLE_ADMIN") {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        } elseif ($security->getUser() == $task->getUserId()) {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        }
        return $this->redirectToRoute('app_task_list');
    }
}
