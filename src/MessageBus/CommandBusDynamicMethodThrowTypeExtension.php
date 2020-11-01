<?php
declare(strict_types = 1);

namespace Nepada\PHPStan\MessageBus;

use Nepada\MessageBus\Commands\Command;
use Nepada\MessageBus\Commands\CommandBus;
use Pepakriz\PHPStanExceptionRules\DynamicMethodThrowTypeExtension;
use Pepakriz\PHPStanExceptionRules\UnsupportedClassException;
use Pepakriz\PHPStanExceptionRules\UnsupportedFunctionException;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VoidType;

class CommandBusDynamicMethodThrowTypeExtension implements DynamicMethodThrowTypeExtension
{

    private CommandTypeExtractor $commandTypeExtractor;

    private CommandHandlerResolver $commandHandlerResolver;

    private Broker $broker;

    public function __construct(CommandTypeExtractor $commandTypeExtractor, CommandHandlerResolver $commandHandlerResolver, Broker $broker)
    {
        $this->commandTypeExtractor = $commandTypeExtractor;
        $this->commandHandlerResolver = $commandHandlerResolver;
        $this->broker = $broker;
    }

    public function getThrowTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        if (! is_a($methodReflection->getDeclaringClass()->getName(), CommandBus::class, true)) {
            throw new UnsupportedClassException();
        }

        if ($methodReflection->getName() !== 'handle') {
            throw new UnsupportedFunctionException();
        }

        $commandType = $this->commandTypeExtractor->extractCommandType($methodCall, $scope);

        if ($commandType === null || $commandType === Command::class) {
            return $this->getThrowTypeFromMethodReflection($methodReflection);
        }

        $types = [];
        foreach ($this->commandHandlerResolver->getHandlerClasses($commandType) as $handlerType) {
            $handler = $this->broker->getClass($handlerType);
            $handleMethod = $handler->getMethod('__invoke', $scope);
            $types[] = $this->getThrowTypeFromMethodReflection($handleMethod);
        }

        return TypeCombinator::union(...$types);
    }

    private function getThrowTypeFromMethodReflection(MethodReflection $methodReflection): Type
    {
        return $methodReflection->getThrowType() ?? new VoidType();
    }

}
