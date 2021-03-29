<?php


namespace App\Domain\Entity;


use App\Shared\Domain\Entity\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Project extends AggregateRoot
{
    private string $id;
    private string $path;
    private string $name;
    private string $version;
    private Collection $sons;
    private Collection $parents;

    private function __construct(string $id, string $path, string $name, string $version)
    {
        $this->id = $id;
        $this->path = $path;
        $this->name = $name;
        $this->version = $version;
        $this->sons = new ArrayCollection();
        $this->parents = new ArrayCollection();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function sons(): Collection
    {
        return $this->sons;
    }

    public function parents(): Collection
    {
        return $this->parents;
    }

    public static function fromPrimitives(string $id, string $path, string $name, string $version)
    {
        return new self($id, $path, $name, $version);
    }
}