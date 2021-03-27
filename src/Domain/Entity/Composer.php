<?php

namespace App\Domain\Entity;

use App\Domain\ComposerNotExistsException;
use App\Domain\ComposerNotHasNameException;
use App\Domain\ComposerNotValidJsonException;

class Composer
{
    private Repo $repo;
    private array $data;

    private function __construct(Repo $repo)
    {
        $file = $repo->path() . '/composer.json';
        if (!file_exists($file)) {
            throw new ComposerNotExistsException($repo->path());
        }

        $data = json_decode(file_get_contents($file), true);
        if (!is_array($data)) {
            throw new ComposerNotValidJsonException($repo->path());
        }

        if (!isset($data['name'])) {
            throw new ComposerNotHasNameException($repo->path());
        }

        $this->data = $data;
        $this->repo = $repo;
    }

    public function name(): string
    {
        return $this->data['name'];
    }

    public static function fromRepo(Repo $repo)
    {
        return new self($repo);
    }
}