<?php

namespace App\Domain\Entity;

use App\Domain\ComposerNotExistsException;
use App\Domain\ComposerNotHasNameException;
use App\Domain\ComposerNotValidJsonException;

class Composer
{
    private array $data;

    private function __construct(string $path)
    {
        $file = $path . '/composer.json';
        if (!file_exists($file)) {
            throw new ComposerNotExistsException($file);
        }

        $data = json_decode(file_get_contents($file), true);
        if (!is_array($data)) {
            throw new ComposerNotValidJsonException($file);
        }

        if (!isset($data['name'])) {
            throw new ComposerNotHasNameException($file);
        }

        $this->data = $data;
    }

    public function name(): string
    {
        return $this->data['name'];
    }

    public function require(): array
    {
        return (isset($this->data['require'])) ? $this->data['require'] : [];
    }

    public static function fromPath(string $path)
    {
        return new self($path);
    }
}