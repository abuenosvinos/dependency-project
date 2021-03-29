<?php

namespace App\Application\Dependencies;

use App\Shared\Domain\Bus\Query\Query;

final class DependenciesQuery extends Query
{
    private string $project;

    public function __construct(string $project)
    {
        parent::__construct();

        $this->project = $project;
    }

    public function project(): string
    {
        return $this->project;
    }
}