<?php

namespace App\Domain;

final class DuplicateProjectPathException extends \LogicException
{
    public function __construct($path)
    {
        parent::__construct(sprintf('Ya existe un proyecto con el mismo path: %s', $path));
    }
}