<?php

namespace App\Client\Controller;

use App\Client\Entity\User;
use App\Client\Controller\ApiController;

class Client  extends ApiController
{
    private string $url;

    public function __construct($url){
        $this->url = $url;
    }

    //Users---------------------------------------------

    public function tokenCheck(User $user)
    {
        $body = [
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
        ];
        $url = $this->url . '/token/check';

        $response = $this->postData($body, $url);

        $user->setRefreshToken($response->refresh_token);

        return $response->token;
    }

    public function tokenRefresh(User $user)
    {
        $body = [
            'refresh_token' => $user->getRefreshToken(),
        ];
        $url = $this->url . '/token/refresh';

        $response = $this->postData($body, $url);

        $user->setRefreshToken($response->refresh_token);

        return $response->token;
    }

    public function register(string $username, string $password)
    {
        $body = [
            'username' => $username,
            'password' => $password,
        ];
        $url = $this->url . '/token/refresh';

        return $this->postData($body, $url);
    }

    //Todos---------------------------------------------

    public function newTodo(User $user, string $description)
    {
        $body = [
            'description' => $description,
        ];
        $url = $this->url . '/todo/self';
        $token = $this->tokenRefresh($user);

        return $this->postData($body, $url, $token);
    }

    public function showAllTodo(User $user, int $id)
    {
        $url = $this->url . '/todo/self';
        $token = $this->tokenRefresh($user);

        return $this->getData($url, $token);
    }

    public function showTodo(User $user, int $id)
    {
        $url = $this->url . '/todo/self/' . $id;
        $token = $this->tokenRefresh($user);

        return $this->getData($url, $token);
    }

    public function editTodo(User $user, int $id, string $description, bool $done)
    {
        $body = [
            'description' => $description,
            'done' => $done,
        ];
        $url = $this->url . '/todo/self/' . $id;
        $token = $this->tokenRefresh($user);

        return $this->putData($body, $url, $token);
    }

    public function deleteTodo(User $user, int $id)
    {
        $url = $this->url . '/todo/self/' . $id;
        $token = $this->tokenRefresh($user);

        return $this->deleteData($url, $token);
    }
}