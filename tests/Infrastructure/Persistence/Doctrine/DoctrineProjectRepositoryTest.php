<?php

namespace App\Tests\Infrastructure\Persistence\Doctrine;

use App\Domain\DuplicateProjectIdException;
use App\Domain\DuplicateProjectNameAndVersionException;
use App\Domain\DuplicateProjectPathException;
use App\Infrastructure\Persistence\Doctrine\DoctrineProjectRepository;
use App\Tests\Shared\Domain\ProjectMother;
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
            ['project' => ProjectMother::create(id: 'id1', path: 'path1', name: 'name1', version: '3.*'), 'dependencies' => []],
            ['project' => ProjectMother::create(name: 'name2'), 'dependencies' => []],
            ['project' => ProjectMother::create(name: 'name3'), 'dependencies' => ['name1']],
            ['project' => ProjectMother::create(name: 'name4'), 'dependencies' => ['name1','name2']],
            ['project' => ProjectMother::create(name: 'name5'), 'dependencies' => ['name3','name4']],
        ];

        foreach ($data as $item) {
            $project = $item['project'];
            foreach ($item['dependencies'] as $son) {
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
            ['project' => ProjectMother::create(id: 'id1')],
            ['project' => ProjectMother::create(id: 'id1')],
        ];

        foreach ($data as $item) {
            $projectRepository->save($item['project']);
        }
    }

    public function testDuplicatePath()
    {
        $this->expectException(DuplicateProjectPathException::class);

        $projectRepository = new DoctrineProjectRepository($this->entityManager);

        $projectRepository->reset();

        $data = [
            ['project' => ProjectMother::create(path: 'path1')],
            ['project' => ProjectMother::create(path: 'path1')],
        ];

        foreach ($data as $item) {
            $projectRepository->save($item['project']);
        }
    }

    public function testDuplicateNameAndVersion()
    {
        $this->expectException(DuplicateProjectNameAndVersionException::class);

        $projectRepository = new DoctrineProjectRepository($this->entityManager);

        $projectRepository->reset();

        $data = [
            ['project' => ProjectMother::create(name: 'name1', version: '3.*')],
            ['project' => ProjectMother::create(name: 'name1', version: '3.*')],
        ];

        foreach ($data as $item) {
            $projectRepository->save($item['project']);
        }
    }
}
