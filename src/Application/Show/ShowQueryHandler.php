<?php

namespace App\Application\Show;

use App\Domain\ProjectRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;

final class ShowQueryHandler implements QueryHandler
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