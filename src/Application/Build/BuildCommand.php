<?php

namespace App\Application\Build;

use App\Shared\Domain\Bus\Command\Command;

final class BuildCommand extends Command
{
    private array $repos;

    public function __construct(array $repos)
    {
        parent::__construct();

        $this->repos = $repos;
    }

    public function repos(): array
    {
        return $this->repos;
    }
}