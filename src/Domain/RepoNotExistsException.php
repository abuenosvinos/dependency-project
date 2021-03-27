<?php

namespace App\Domain;

final class RepoNotExistsException extends \DomainException
{
    public function __construct($path)
    {
        parent::__construct(sprintf('No existe el repo indicado: %s', $path));
    }
}