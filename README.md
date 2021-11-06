PHPStan Message Bus extension
=============================

[![Build Status](https://github.com/nepada/phpstan-message-bus/workflows/CI/badge.svg)](https://github.com/nepada/phpstan-message-bus/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/nepada/phpstan-message-bus/badge.svg?branch=master)](https://coveralls.io/github/nepada/phpstan-message-bus?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/phpstan-message-bus.svg)](https://packagist.org/packages/nepada/phpstan-message-bus)
[![Latest stable](https://img.shields.io/packagist/v/nepada/phpstan-message-bus.svg)](https://packagist.org/packages/nepada/phpstan-message-bus)


* [PHPStan](https://github.com/phpstan/phpstan)
* [nepada/message-bus](https://github.com/nepada/message-bus)


Installation
------------

Via Composer:

```sh
composer require --dev nepada/phpstan-mesasge-bus
```

Unless you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) you need to manually enable the extension in your config:

```yaml
includes:
    - vendor/nepada/phpstan-message-bus/extension.neon
```

Either way, you need to specify the directories in which your command handlers are located:
```yaml
parameters:
    commandHandlerDirectories:
        - app
        - src
```


Description
-----

The package currently provides only one extension - `DynamicMethodThrowTypeExtension`. The extension propagates exception thrown by command handlers up to the command bus caller.

```php


final class FooService
{

    private \Nepada\MessageBus\Commands\CommandBus $commandBus;

    public function __construct(\Nepada\MessageBus\Commands\CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function placeOrder(): void
    {
        try {
            $command = new PlaceOrderCommand();
            $this->commandBus->handle($command);
        } catch (FailedToPlaceOrderException $exception) {
            // FailedToPlaceOrderException may be thrown and needs to handled
        }
    }

}

final class PlaceOrderCommand implements \Nepada\MessageBus\Commands\Command
{

}

final class PlaceOrderHandler implements \Nepada\MessageBus\Commands\CommandHandler
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

class FailedToPlaceOrderException extends \RuntimeException
{

}

```
