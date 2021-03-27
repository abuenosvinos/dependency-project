<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine;

use App\Domain\DuplicateProjectIdException;
use App\Domain\DuplicateProjectNameAndVersionException;
use App\Domain\DuplicateProjectPathException;
use App\Domain\Entity\Project;
use App\Infrastructure\Persistence\Doctrine\DoctrineProjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineProjectRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testAddProjects()
    {
        $projectRepository = new DoctrineProjectRepository($this->entityManager);

        $projectRepository->reset();

        $data = [
            ['id1','path1','name1','3.*', []],
            ['id2','path2','name2','8.1', []],
            ['id3','path3','name3','6', ['name1']],
            ['id4','path4','name4','^2.8', ['name1','name2']],
            ['id5','path5','name5','5.2.*', ['name3','name4']],
        ];

        foreach ($data as $item) {
            $project = Project::fromPrimitives($item[0], $item[1], $item[2], $item[3]);
            foreach ($item[4] as $son) {
                $projectSon = $projectRepository->findByName($son);
                $project->sons()->add($projectSon);
                $projectSon->parents()->add($project);
            }
            $projectRepository->save($project);
        }

        $list = $projectRepository->searchAll();
        $this->assertEquals(count($list), 5);

        $project1 = $projectRepository->findByName('name1');
        $this->assertEquals($project1->id(), 'id1');
        $this->assertEquals($project1->path(), 'path1');
        $this->assertEquals($project1->version(), '3.*');
        $this->assertEquals($project1->sons()->count(), 0);
        $this->assertEquals($project1->parents()->count(), 2);

        $project2 = $projectRepository->findByName('name2');
        $this->assertEquals(count($project2->sons()), 0);
        $this->assertEquals(count($project2->parents()), 1);

        $project3 = $projectRepository->findByName('name3');
        $this->assertEquals(count($project3->sons()), 1);
        $this->assertEquals(count($project3->parents()), 1);

        $project4 = $projectRepository->findByName('name4');
        $this->assertEquals(count($project4->sons()), 2);
        $this->assertEquals(count($project4->parents()), 1);

        $project5 = $projectRepository->findByName('name5');
        $this->assertEquals(count($project5->sons()), 2);
        $this->assertEquals(count($project5->parents()), 0);
    }

    public function testDuplicateId()
    {
        $this->expectException(DuplicateProjectIdException::class);

        $projectRepository = new DoctrineProjectRepository($this->entityManager);

        $projectRepository->reset();

        $data = [
            ['id1','path1','name1','3.*', []],
            ['id1','path1','name2','8.1', []]
        ];

        foreach ($data as $item) {
            $project = Project::fromPrimitives($item[0], $item[1], $item[2], $item[3]);
            $projectRepository->save($project);
        }
    }

    public function testDuplicatePath()
    {
        $this->expectException(DuplicateProjectPathException::class);

        $projectRepository = new DoctrineProjectRepository($this->entityManager);

        $projectRepository->reset();

        $data = [
            ['id1','path1','name1','3.*', []],
            ['id2','path1','name2','8.1', []]
        ];

        foreach ($data as $item) {
            $project = Project::fromPrimitives($item[0], $item[1], $item[2], $item[3]);
            $projectRepository->save($project);
        }
    }


    public function testDuplicateNameAndVersion()
    {
        $this->expectException(DuplicateProjectNameAndVersionException::class);

        $projectRepository = new DoctrineProjectRepository($this->entityManager);

        $projectRepository->reset();

        $data = [
            ['id1','path1','name1','3.*', []],
            ['id2','path2','name1','3.*', []]
        ];

        foreach ($data as $item) {
            $project = Project::fromPrimitives($item[0], $item[1], $item[2], $item[3]);
            $projectRepository->save($project);
        }
    }
}
