<?php
declare(strict_types = 1);

namespace Nepada\PHPStan\MessageBus;

use PHPStan\Analyser\Scope;
use PHPStan\Type\TypeWithClassName;
use PhpParser\Node\Expr\MethodCall;

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
