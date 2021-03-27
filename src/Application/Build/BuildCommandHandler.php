<?php

namespace App\Application\Build;

use App\Domain\Entity\Project;
use App\Domain\Entity\Repo;
use App\Domain\ProjectRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BuildCommandHandler implements MessageHandlerInterface
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(BuildCommand $buildCommand)
    {
        $this->projectRepository->reset();

        $repos = $buildCommand->repos();

        $validProjects = [];
        /** @var Repo $repo */
        foreach ($repos as $repo) {
            $project = Project::fromPrimitives(uniqid(), $repo->path(), $repo->composer()->name(), $repo->version());
            // TODO; La clave debería ser name+version
            $validProjects[$repo->composer()->name()] = $project;
            $this->projectRepository->save($project);
        }
        $validNames = array_keys($validProjects);

        /** @var Repo $repo */
        foreach ($repos as $repo) {
            $requires = $repo->composer()->require();
            foreach ($requires as $name => $version) {
                // TODO; La clave debería ser name+version
                if (in_array($name, array_values($validNames))) {
                    $projectParent = $validProjects[$repo->composer()->name()];
                    $projectSon = $validProjects[$name];
                    $projectParent->sons()->add($projectSon);
                }
            }
        }

        foreach ($validProjects as $project) {
            $this->projectRepository->save($project);
        }
    }
}