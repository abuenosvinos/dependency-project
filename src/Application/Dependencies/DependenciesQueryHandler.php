<?php

namespace App\Application\Dependencies;

use App\Domain\Entity\Project;
use App\Domain\ProjectNotExistsException;
use App\Domain\ProjectRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DependenciesQueryHandler implements MessageHandlerInterface
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(DependenciesQuery $dependenciesQuery)
    {
        $project = $this->projectRepository->findByName($dependenciesQuery->project());
        if (!$project) {
            throw new ProjectNotExistsException($dependenciesQuery->project());
        }

        $listToUpdate = $this->findChanges($project, []);
        return array_values($listToUpdate);
    }

    private function findChanges(Project $project, array $listToUpdate): array
    {
        $parents = $project->parents();
        /** @var Project $parent */
        foreach ($parents as $parent) {
            if (!in_array($parent->name(), $listToUpdate)) {
                $listToUpdate[$parent->name()] = $parent;
            }
            $listToUpdate = $this->findChanges($parent, $listToUpdate);
        }

        return $listToUpdate;
    }
}