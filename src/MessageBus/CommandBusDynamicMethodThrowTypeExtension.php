<?php
declare(strict_types = 1);

namespace Nepada\PHPStan\MessageBus;

use Nepada\MessageBus\Commands\Command;
use Nepada\MessageBus\Commands\CommandBus;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\DynamicMethodThrowTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class CommandBusDynamicMethodThrowTypeExtension implements DynamicMethodThrowTypeExtension
{

    private CommandTypeExtractor $commandTypeExtractor;

    private CommandHandlerResolver $commandHandlerResolver;

    private ReflectionProvider $reflectionProvider;

    public function __construct(CommandTypeExtractor $commandTypeExtractor, CommandHandlerResolver $commandHandlerResolver, ReflectionProvider $reflectionProvider)
    {
        $this->commandTypeExtractor = $commandTypeExtractor;
        $this->commandHandlerResolver = $commandHandlerResolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        if (! $methodReflection->getDeclaringClass()->is(CommandBus::class)) {
            return false;
        }

        return $methodReflection->getName() === 'handle';
    }

    public function getThrowTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type
    {
        $throwTypes = [];

        $commandBusThrowType = $methodReflection->getThrowType();
        if ($commandBusThrowType !== null && ! $commandBusThrowType->isVoid()->yes()) {
            $throwTypes[] = $commandBusThrowType;
        }

        $commandTypes = $this->commandTypeExtractor->extractCommandType($methodCall, $scope);
        foreach ($commandTypes as $commandType) {
            if ($commandType === Command::class) {
                continue;
            }
            $throwTypes = [
                ...$throwTypes,
                ...$this->getHandlerThrowTypes($commandType, $scope),
            ];
        }

        if (count($throwTypes) === 0) {
            return null;
        }

        return TypeCombinator::union(...$throwTypes);
    }

    /**
     * @return list<Type>
     */
    private function getHandlerThrowTypes(string $commandType, Scope $scope): array
    {
        $throwTypes = [];

        foreach ($this->commandHandlerResolver->getHandlerClasses($commandType) as $handlerType) {
            if (! $this->reflectionProvider->hasClass($handlerType)) {
                continue;
            }
            $handlerReflection = $this->reflectionProvider->getClass($handlerType);
            if (! $handlerReflection->hasMethod('__invoke')) {
                continue;
            }
            $handlerThrowType = $handlerReflection->getMethod('__invoke', $scope)->getThrowType();
            if ($handlerThrowType === null || $handlerThrowType->isVoid()->yes()) {
                continue;
            }
            $throwTypes[] = $handlerThrowType;
        }

        return $throwTypes;
    }

}
