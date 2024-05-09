<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use App\Form\UpdateUserType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users', name: 'app_user_list')]
    public function listUsers(UserRepository $users): Response
    {
        return $this->render('user/list.html.twig', ['users' => $users->findAll()]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/create', name: 'app_user_create')]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', "Nouvel utilisateur créé");
            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}/edit', name: 'app_user_edit')]
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UpdateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $form->getData();
            $entityManager->persist($currentUser);
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('app_user_list');
        }
        
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/users/{id}/delete', name: 'app_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $userId = $user->getId();
        $tasks = $taskRepository->findBy(['userId' => $userId]);

        foreach ($tasks as $task) {
            $task->setUserId(null);
            $entityManager->persist($task);
        }

        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', "L'utilisateur a bien été supprimé");
        return $this->redirectToRoute('app_user_list');
    }
}
