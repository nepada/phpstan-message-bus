<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus\Fixtures;

use Nepada\MessageBus\Commands\CommandHandler;

final class RejectOrderHandler implements CommandHandler
{

    /**
     * @param RejectOrderCommand $command
     * @throws FailedToRejectOrderException
     */
    public function __invoke(RejectOrderCommand $command): void
    {
        throw new FailedToRejectOrderException('Failed to place order');
    }

}
