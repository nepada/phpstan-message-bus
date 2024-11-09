<?php
declare(strict_types = 1);

namespace Nepada\PHPStan\MessageBus;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\VariadicPlaceholder;
use PHPStan\Analyser\Scope;

class CommandTypeExtractor
{

    /**
     * @return list<string>
     */
    public function extractCommandType(MethodCall $methodCall, Scope $scope): array
    {
        if (count($methodCall->args) === 0) {
            return [];
        }

        $commandArgument = $methodCall->args[0];
        if ($commandArgument instanceof VariadicPlaceholder) {
            return [];
        }

        return $scope->getType($commandArgument->value)->getObjectClassNames();
    }

}
