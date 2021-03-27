<?php

namespace App\Domain;

final class DuplicateProjectIdException extends \LogicException
{
    public function __construct($id)
    {
        parent::__construct(sprintf('Ya existe un proyecto con el mismo id: %s', $id));
    }
}