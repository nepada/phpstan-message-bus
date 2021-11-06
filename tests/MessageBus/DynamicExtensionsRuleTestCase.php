<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @template TRule of Rule
 * @extends RuleTestCase<TRule>
 */
abstract class DynamicExtensionsRuleTestCase extends RuleTestCase
{

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return array_merge(
            parent::getAdditionalConfigFiles(),
            [
                __DIR__ . '/../../extension.neon',
                __DIR__ . '/Fixtures/config.neon',
            ],
        );
    }

}
