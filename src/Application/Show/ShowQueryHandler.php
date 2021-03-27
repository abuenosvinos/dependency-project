<?php

namespace App\Application\Show;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ShowQueryHandler implements MessageHandlerInterface
{
    public function __invoke(ShowQuery $showQuery)
    {
        dump('ShowQueryHandler');
        return "a";
    }
}