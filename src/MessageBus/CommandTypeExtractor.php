<?php
declare(strict_types = 1);

namespace Nepada\PHPStan\MessageBus;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;

class CommandTypeExtractor
{

    public function extractCommandType(MethodCall $methodCall, Scope $scope): ?string
    {
        if (count($methodCall->args) === 0) {
            return null;
        }

        $commandType = $scope->getType($methodCall->args[0]->value);
        if (! $commandType instanceof TypeWithClassName) {
            return null;
        }

        return $commandType->getClassName();
    }

}
