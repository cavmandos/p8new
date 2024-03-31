<?php

namespace App\Tests;

use App\Repository\UserRepository;

class UserControllerTest extends AbstractControllerTest
{
    private function getUserId(): int
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy([], ['id' => 'desc']);
        return $getUser->getId();
    }
    
    public function testIfNotLogged(): void
    {
        $this->client->request('GET', '/users');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testIfLoggedAsUser(): void
    {
        $this->authenticateUser();
        $this->client->request('GET', '/users');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testIfLoggedAsAdmin(): void
    {
        $this->authenticateAdmin();
        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserIfNotLogged(): void
    {
        $this->client->request('GET', '/users/create');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testCreateUserIfLoggedAsUser(): void
    {
        $this->authenticateUser();
        $this->client->request('GET', '/users/create');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserIfLoggedAsAdmin(): void
    {
        $this->authenticateAdmin();
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Créer l'utilisateur")->form();
        $form['register_user[username]'] = 'testUserViaTest';
        $form['register_user[plainPassword][first]'] = 'password';
        $form['register_user[plainPassword][second]'] = 'password';
        $form['register_user[email]'] = 'testviatest@mail.com';
        $form['register_user[roles]'] = 'ROLE_USER';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testUpdateUserIfNotLogged(): void
    {
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/edit");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testUpdateUserLoggedAsUser(): void
    {
        $this->authenticateUser();
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/edit");
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateUserLoggedAsAdmin(): void
    {
        $this->authenticateAdmin();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $getUser = $userRepository->findOneBy(["email" => "testviatest@mail.com"]);
        $getUserId = $getUser->getId();

        $crawler = $this->client->request('GET', "/users/$getUserId/edit");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['update_user[username]'] = 'testUpdateUser';
        $form['update_user[roles]'] = 'ROLE_ADMIN';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testDeleteUserIfNotLogged()
    {
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals("/login", parse_url($this->client->followRedirect()->getUri(), PHP_URL_PATH));
    }

    public function testDeleteUserIfLoggedAsUser()
    {
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteUserIfLoggedAsAdmin()
    {
        $this->authenticateAdmin();
        $id = $this->getUserId();
        $this->client->request('GET', "/users/$id/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
}