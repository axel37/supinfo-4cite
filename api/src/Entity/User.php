<?php

namespace App\Entity;

use App\Exception\EmptyEmailException;
use App\Exception\EmptyNameException;

class User
{
    public function __construct(private string $email, private string $username)
    {
        if (trim($this->email) === '') {
            throw new EmptyEmailException();
        }
        if (trim($this->username) === '') {
            throw new EmptyNameException();
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
