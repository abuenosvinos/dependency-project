<?php

namespace App\Application\Build;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BuildCommandHandler implements MessageHandlerInterface
{
    public function __invoke(BuildCommand $buildCommand)
    {
        dump('BuildCommandHandler');
    }
}