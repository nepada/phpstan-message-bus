<?php
declare(strict_types = 1);

namespace NepadaTests\Fixtures;

use Nepada\MessageBus\Commands\CommandBus;

final class TestService
{

    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function placeOrder(): void
    {
        try {
            $command = new PlaceOrderCommand();
            $this->commandBus->handle($command);
        } catch (FailedToPlaceOrderException $exception) {
            // noop
        }
    }

    public function rejectOrder(): void
    {
        try {
            $command = new RejectOrderCommand();
            $this->commandBus->handle($command);
        } catch (FailedToRejectOrderException $exception) {
            // noop
        }
    }

    public function handleOrderCommand(OrderCommand $command): void
    {
        try {
            $this->commandBus->handle($command);
        } catch (FailedToPlaceOrderException $exception) {
            // noop
        } catch (FailedToRejectOrderException $exception) {
            // noop
        }
    }

    /**
     * @param OrderCommand $command
     * @throws FailedToPlaceOrderException
     * @throws FailedToRejectOrderException
     */
    public function handleOrderCommandWithoutErrorHandling(OrderCommand $command): void
    {
        $this->commandBus->handle($command);
    }

}
