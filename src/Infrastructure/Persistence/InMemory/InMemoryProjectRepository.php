<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\InMemory;

use App\Domain\DuplicateProjectIdException;
use App\Domain\DuplicateProjectNameAndVersionException;
use App\Domain\DuplicateProjectPathException;
use App\Domain\Entity\Project;
use App\Domain\ProjectRepository;

final class InMemoryProjectRepository implements ProjectRepository
{
    private array $projects = [];

    public function save(Project $project): void
    {
        /** @var Project $projectInMemory */
        foreach ($this->projects as &$projectInMemory) {
            if ($projectInMemory === $project) {
                $projectInMemory = $project;
                return;
            }

            if ($projectInMemory->id() == $project->id()) {
                throw new DuplicateProjectIdException($project->id());
            }

            if ($projectInMemory->path() == $project->path()) {
                throw new DuplicateProjectPathException($project->path());
            }

            if (($projectInMemory->name() == $project->name()) && ($projectInMemory->version() == $project->version())) {
                throw new DuplicateProjectNameAndVersionException($project->name(), $project->version());
            }
        }

        $this->projects[] = $project;
    }

    public function findByName(string $name): ?Project
    {
        /** @var Project $projectInMemory */
        foreach ($this->projects as $projectInMemory) {
            if ($projectInMemory->name() == $name) {
                return $projectInMemory;
            }
        }

        return null;
    }

    public function searchAll(): array
    {
        return $this->projects;
    }

    public function reset(): void
    {
        $this->projects = [];
    }
}
