<?php

namespace App\Domain;

final class ProjectNotExistsException extends \DomainException
{
    public function __construct($name)
    {
        parent::__construct(sprintf('No existe el proyecto indicado: %s', $name));
    }
}