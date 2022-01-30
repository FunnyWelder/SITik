<?php

namespace App\Client\Entity;

class User
{
    private string $username;
    private string $password;
    private ?string $refreshToken;

    public function __construct($login, $password)
    {
        $this->username = $login;
        $this->password = $password;
        $this->refreshToken = null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }
}