<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\DuplicateProjectIdException;
use App\Domain\DuplicateProjectNameAndVersionException;
use App\Domain\DuplicateProjectPathException;
use App\Domain\Entity\Project;
use App\Domain\ProjectRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

final class DoctrineProjectRepository extends DoctrineRepository implements ProjectRepository
{
    public function save(Project $project): void
    {
        try {
            $this->persist($project);
        } catch (UniqueConstraintViolationException $e) {
            if (str_contains($e->getMessage(), 'for key \'PRIMARY\'')) {
                throw new DuplicateProjectIdException($project->id());
            } else if (str_contains($e->getMessage(), 'project_path_idx')) {
                throw new DuplicateProjectPathException($project->path());
            } else if (str_contains($e->getMessage(), 'project_name_version_idx')) {
                throw new DuplicateProjectNameAndVersionException($project->name(), $project->version());
            }
        }
    }

    public function findByName(string $name): Project
    {
        return $this->repository(Project::class)->findOneByName($name);
    }

    public function searchAll(): array
    {
        return $this->repository(Project::class)->findAll();
    }

    public function reset(): void
    {
        $list = $this->repository(Project::class)->findAll();
        foreach ($list as $item) {
            $this->entityManager()->remove($item);
        }
        $this->entityManager()->flush();
    }
}
