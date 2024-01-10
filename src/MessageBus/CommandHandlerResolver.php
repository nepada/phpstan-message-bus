<?php
declare(strict_types = 1);

namespace Nepada\PHPStan\MessageBus;

use Nepada\MessageBus\Commands\CommandHandler;
use Nepada\MessageBus\StaticAnalysis\HandlerType;
use Nepada\MessageBus\StaticAnalysis\MessageTypeExtractor;
use Nepada\MessageBus\StaticAnalysis\ReflectionHelper;
use Nepada\MessageBus\StaticAnalysis\StaticAnalysisFailedException;
use Nette\Loaders\RobotLoader;

class CommandHandlerResolver
{

    /**
     * @var string[]
     */
    private array $scanDirectories;

    /**
     * @var string[]|null
     */
    private ?array $commandTypeByHandlerType = null;

    private MessageTypeExtractor $messageTypeExtractor;

    /**
     * @param string[] $scanDirectories
     */
    public function __construct(array $scanDirectories, MessageTypeExtractor $messageTypeExtractor)
    {
        $this->scanDirectories = $scanDirectories;
        $this->messageTypeExtractor = $messageTypeExtractor;
    }

    /**
     * @return string[]
     */
    public function getHandlerClasses(string $handledCommandType): array
    {
        $handlers = [];
        foreach ($this->getCommandTypeByHandlerType() as $handlerType => $commandType) {
            if (is_a($commandType, $handledCommandType, true)) {
                $handlers[] = $handlerType;
            }
        }

        return $handlers;
    }

    /**
     * @return string[]
     */
    private function getCommandTypeByHandlerType(): array
    {
        if ($this->commandTypeByHandlerType !== null) {
            return $this->commandTypeByHandlerType;
        }

        $this->commandTypeByHandlerType = [];
        /** @var class-string $class */
        foreach ($this->getAllClasses() as $class) {
            if (! is_a($class, CommandHandler::class, true)) {
                continue;
            }

            try {
                $handlerReflection = ReflectionHelper::requireClassReflection($class);
                if ($handlerReflection->isTrait() || $handlerReflection->isInterface() || $handlerReflection->isAbstract()) {
                    continue;
                }

                $messageType = $this->messageTypeExtractor->extract(HandlerType::fromString($class));
                $this->commandTypeByHandlerType[$class] = $messageType->toString();

            } catch (StaticAnalysisFailedException $exception) {
                // noop
            }
        }

        return $this->commandTypeByHandlerType;
    }

    /**
     * @return string[]
     */
    private function getAllClasses(): array
    {
        $loader = $this->createRobotLoader();

        return array_keys($loader->getIndexedClasses());
    }

    private function createRobotLoader(): RobotLoader
    {
        $loader = new RobotLoader();
        foreach ($this->scanDirectories as $directory) {
            $loader->addDirectory($directory);
        }
        $loader->rebuild();

        return $loader;
    }

}
