<?php

namespace App\Tests\Domain\Entity;

use App\Domain\ComposerNotExistsException;
use App\Domain\ComposerNotHasNameException;
use App\Domain\ComposerNotValidJsonException;
use App\Domain\Entity\Composer;
use App\Domain\Entity\Repo;
use App\Domain\RepoNotExistsException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ComposerTest extends KernelTestCase
{
    private string $path_repositories;

    protected function setUp(): void
    {
        self::bootKernel();

        parent::setUp();

        $container = self::$kernel->getContainer();

        $this->path_repositories = $container->getParameter('path_repositories');
    }

    public function testExistsAndValid()
    {
        $finalPath = $this->path_repositories . 'esteesbueno';
        $repo = Repo::fromPrimitives($finalPath);

        $composer = Composer::fromRepo($repo);

        $this->assertEquals('siquesoybueno', $composer->name());
    }

    public function testNotExists()
    {
        $this->expectException(ComposerNotExistsException::class);

        $finalPath = $this->path_repositories . 'project1';
        $repo = Repo::fromPrimitives($finalPath);

        Composer::fromRepo($repo);
    }

    public function testNotValidJson()
    {
        $this->expectException(ComposerNotValidJsonException::class);

        $finalPath = $this->path_repositories . 'project2';
        $repo = Repo::fromPrimitives($finalPath);

        Composer::fromRepo($repo);
    }


    public function testNotHasName()
    {
        $this->expectException(ComposerNotHasNameException::class);

        $finalPath = $this->path_repositories . 'project3';
        $repo = Repo::fromPrimitives($finalPath);

        $composer = Composer::fromRepo($repo);
    }
}
