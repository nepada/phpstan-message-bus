<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus\Fixtures;

use Nepada\MessageBus\Commands\CommandHandler;

final class PlaceOrderHandler implements CommandHandler
{

    /**
     * @param PlaceOrderCommand $command
     * @throws FailedToPlaceOrderException
     */
    public function __invoke(PlaceOrderCommand $command): void
    {
        throw new FailedToPlaceOrderException('Failed to place order');
    }

}
