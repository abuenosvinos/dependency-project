<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Repo;
use App\Domain\RepoNotExistsException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RepoTest extends KernelTestCase
{
    private string $path_repositories;

    protected function setUp(): void
    {
        self::bootKernel();

        parent::setUp();

        $container = self::$kernel->getContainer();

        $this->path_repositories = $container->getParameter('path_repositories');
    }

    public function testExists()
    {
        $finalPath = $this->path_repositories . 'esteesbueno';
        $repo = Repo::fromPrimitives($finalPath);

        $this->assertEquals($finalPath, $repo->path());
    }

    public function testNotExists()
    {
        $this->expectException(RepoNotExistsException::class);

        $finalPath = $this->path_repositories . 'noexisto';
        $repo = Repo::fromPrimitives($finalPath);
    }
}
