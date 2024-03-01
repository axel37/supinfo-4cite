<?php

namespace App\Entity;

use App\Exception\EmptyEmailException;

class User
{
    public function __construct(private string $email)
    {
        if (trim($this->email) === '') {
            throw new EmptyEmailException();
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
