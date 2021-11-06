<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus\Fixtures;

use Nepada\MessageBus\Commands\Command;
use Nepada\MessageBus\Commands\CommandBus;

final class DummyCommandBus implements CommandBus
{

    /**
     * @param Command $command
     * @throws NotImplementedException
     */
    public function handle(Command $command): void
    {
        throw new NotImplementedException();
    }

}
