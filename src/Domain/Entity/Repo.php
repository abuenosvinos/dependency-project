<?php

namespace App\Domain\Entity;

use App\Domain\RepoNotExistsException;

class Repo
{
    private string $path;
    private Composer $composer;
    private string $version;

    private function __construct(string $path)
    {
        if (!is_dir($path)) {
            throw new RepoNotExistsException($path);
        }

        /*
        TODO; Faltaría calcular la versión del proyecto/librería

        // V1 para obtener la versión
        exec('git symbolic-ref HEAD', $output);
        $branches = explode('/', $output[0]);
        $branch = end($branches);

        // V2 para obtener la versión
        $branch = explode('/',trim(file_get_contents('./.git/HEAD')))[2];
        */

        $this->composer = Composer::fromPath($path);
        $this->version = rand(1,10);
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function composer(): Composer
    {
        return $this->composer;
    }

    public function version(): string
    {
        return $this->version;
    }

    public static function fromPrimitives(string $path)
    {
        return new self($path);
    }
}