<?php

namespace App\Domain;

final class ComposerNotValidJsonException extends \DomainException
{
    public function __construct($path)
    {
        parent::__construct(sprintf('El fichero composer no es un fichero json válido: %s', $path));
    }
}