<?php


namespace App\Domain;


use App\Domain\Entity\Project;

interface ProjectRepository
{
    public function save(Project $project): void;

    public function findByName(string $name): Project;

    public function searchAll(): array;

    public function reset(): void;
}