<?php

namespace App\Application\Build;

final class BuildCommand
{
    private array $repos;

    public function __construct(array $repos)
    {
        $this->repos = $repos;
    }

    public function repos(): array
    {
        return $this->repos;
    }
}