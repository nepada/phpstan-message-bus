<?php
declare(strict_types = 1);

namespace NepadaTests\Fixtures;

use Nepada\MessageBus\Commands\CommandBus;

final class InvalidTestService
{

    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function missingThrowsAnnotation(OrderCommand $command): void
    {
        $this->commandBus->handle($command);
    }

}
