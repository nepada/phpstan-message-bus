<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

$config = [];

if (InstalledVersions::satisfies(new VersionParser(), 'phpstan/phpstan', '<1.10.36')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Class PHPStan\\\\Rules\\\\Exceptions\\\\CatchWithUnthrownExceptionRule does not have a constructor and must be instantiated without any parameters\\.$~',
        'path' => '../../tests/MessageBus/CatchWithUnthrownExceptionRuleTest.php',
        'count' => 1,
    ];
}

return $config;
