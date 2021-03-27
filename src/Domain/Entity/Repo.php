<?php

namespace App\Domain\Entity;

use App\Domain\RepoNotExistsException;

class Repo
{
    private string $path;

    private function __construct(string $path)
    {
        if (!is_dir($path)) {
            throw new RepoNotExistsException($path);
        }
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public static function fromPrimitives(string $path)
    {
        return new self($path);
    }
}