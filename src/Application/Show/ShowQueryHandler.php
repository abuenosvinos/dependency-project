<?php

namespace App\Application\Show;

use App\Domain\ProjectRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ShowQueryHandler implements MessageHandlerInterface
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(ShowQuery $showQuery)
    {
        return $this->projectRepository->searchAll();
    }
}