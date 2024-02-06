<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user_list')]
    public function listUsers(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/users/create', name: 'app_user_create')]
    public function createUser(): Response
    {
        return $this->render('user/create.html.twig');
    }

    #[Route('/users/{id}/edit', name: 'app_user_edit')]
    public function editUser(): Response
    {
        return $this->render('user/edit.html.twig');
    }
}
