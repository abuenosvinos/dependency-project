<?php

namespace App\Domain;

final class DuplicateProjectNameAndVersionException extends \LogicException
{
    public function __construct($name, $version)
    {
        parent::__construct(sprintf('Ya existe un proyecto con el mismo nombre y versión: %s - $s', $name ,$version));
    }
}