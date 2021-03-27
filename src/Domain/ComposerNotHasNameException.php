<?php

namespace App\Domain;

final class ComposerNotHasNameException extends \DomainException
{
    public function __construct($path)
    {
        parent::__construct(sprintf('El fichero composer no tiene el parámetro obligatorio name: %s', $path));
    }
}