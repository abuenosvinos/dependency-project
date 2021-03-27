<?php

namespace App\Application\Dependencies;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DependenciesQueryHandler implements MessageHandlerInterface
{
    public function __invoke(DependenciesQuery $dependenciesQuery)
    {
        dump('DependenciesQueryHandler');
    }
}