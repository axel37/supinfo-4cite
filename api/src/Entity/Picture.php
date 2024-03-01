<?php

namespace App\Entity;


use App\Exception\EmptyPathException;

class Picture
{

    public function __construct(private string $path)
    {
        if (trim($this->path) === '') {
            throw new EmptyPathException();
        }
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
