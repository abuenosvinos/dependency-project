<?php

namespace App\Domain;

final class ComposerNotExistsException extends \DomainException
{
    public function __construct($path)
    {
        parent::__construct(sprintf('No existe el fichero composer en el repo indicado: %s', $path));
    }
}