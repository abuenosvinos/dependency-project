<?php

namespace App\Tests\Domain\Application;

use App\Application\Build\BuildCommand;
use App\Application\Build\BuildCommandHandler;
use App\Application\Dependencies\DependenciesQuery;
use App\Application\Dependencies\DependenciesQueryHandler;
use App\Application\Show\ShowQuery;
use App\Application\Show\ShowQueryHandler;
use App\Domain\Entity\Repo;
use App\Infrastructure\Persistence\InMemory\InMemoryProjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApplicationTest extends KernelTestCase
{
    private string $path_repositories;

    protected function setUp(): void
    {
        self::bootKernel();

        parent::setUp();

        $container = self::$kernel->getContainer();

        $this->path_repositories = $container->getParameter('path_repositories') . '../good/';
    }

    public function testApplication()
    {
        $projectRepository = new InMemoryProjectRepository();

        $buildHandler = new BuildCommandHandler($projectRepository);
        $buildHandler->__invoke(new BuildCommand([
            Repo::fromPrimitives($this->path_repositories . 'project1'),
            Repo::fromPrimitives($this->path_repositories . 'project2'),
            Repo::fromPrimitives($this->path_repositories . 'library1'),
            Repo::fromPrimitives($this->path_repositories . 'library3'),
            Repo::fromPrimitives($this->path_repositories . 'library5'),
            Repo::fromPrimitives($this->path_repositories . 'library7'),
        ]));

        $showHandler = new ShowQueryHandler($projectRepository);
        $listReposShow = $showHandler->__invoke(new ShowQuery());

        $this->assertEquals(count($listReposShow), 6);
        $this->assertEquals($listReposShow[0]->name(), 'proyectouno');

        $showHandler = new DependenciesQueryHandler($projectRepository);
        $listReposDependencies = $showHandler->__invoke(new DependenciesQuery('libreriasiete'));

        $this->assertEquals(count($listReposDependencies), 4);
    }
}
