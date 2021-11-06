<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus\Fixtures;

use Nepada\MessageBus\Commands\CommandBus;

final class TestService
{

    private CommandBus $commandBus;

    private DummyCommandBus $dummyCommandBus;

    public function __construct(CommandBus $commandBus, DummyCommandBus $dummyCommandBus)
    {
        $this->commandBus = $commandBus;
        $this->dummyCommandBus = $dummyCommandBus;
    }

    public function placeOrder(): void
    {
        try {
            $command = new PlaceOrderCommand();
            $this->commandBus->handle($command);
        } catch (FailedToPlaceOrderException $exception) {
            // ok
        } catch (NotImplementedException $exception) {
            // error
        }
    }

    public function rejectOrder(): void
    {
        try {
            $command = new RejectOrderCommand();
            $this->dummyCommandBus->handle($command);
        } catch (FailedToRejectOrderException $exception) {
            // ok
        } catch (NotImplementedException $exception) {
            // ok
        } catch (\Throwable $exception) {
            // error
        }
    }

    public function handleOrderCommand(OrderCommand $command): void
    {
        $this->commandBus->handle($command);
    }

}
