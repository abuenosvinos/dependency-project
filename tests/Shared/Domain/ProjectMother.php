<?php

declare(strict_types = 1);

namespace App\Tests\Shared\Domain;

use App\Domain\Entity\Project;

final class ProjectMother
{
    public static function create(?string $id = null, ?string $path = null, ?string $name = null, ?string $version = null): Project
    {
        return Project::fromPrimitives(
            $id ?? UuidMother::random(),
            $path ?? StringMother::random(),
            $name ?? StringMother::random(),
            $version ?? StringMother::random()
        );
    }
}