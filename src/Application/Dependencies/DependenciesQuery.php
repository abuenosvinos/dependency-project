<?php

namespace App\Application\Dependencies;

final class DependenciesQuery
{
    private string $project;

    public function __construct(string $project)
    {
        $this->project = $project;
    }

    public function project(): string
    {
        return $this->project;
    }
}