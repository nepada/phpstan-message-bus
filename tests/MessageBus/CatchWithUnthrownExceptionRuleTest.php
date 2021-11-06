<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus;

use PHPStan\Rules\Exceptions\CatchWithUnthrownExceptionRule;
use PHPStan\Rules\Rule;

/**
 * @extends DynamicExtensionsRuleTestCase<CatchWithUnthrownExceptionRule>
 */
class CatchWithUnthrownExceptionRuleTest extends DynamicExtensionsRuleTestCase
{

    protected function getRule(): Rule
    {
        return new CatchWithUnthrownExceptionRule();
    }

    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/TestService.php'],
            [
                [
                    'Dead catch - NepadaTests\PHPStan\MessageBus\Fixtures\NotImplementedException is never thrown in the try block.',
                    28,
                ],
                [
                    'Dead catch - Throwable is never thrown in the try block.',
                    42,
                ],
            ],
        );
    }

}
