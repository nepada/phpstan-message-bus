<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus;

use PHPStan\Rules\Exceptions\MissingCheckedExceptionInMethodThrowsRule;
use PHPStan\Rules\Rule;

/**
 * @extends DynamicExtensionsRuleTestCase<MissingCheckedExceptionInMethodThrowsRule>
 */
class MissingCheckedExceptionInMethodThrowsRuleTest extends DynamicExtensionsRuleTestCase
{

    protected function getRule(): Rule
    {
        /** @var MissingCheckedExceptionInMethodThrowsRule $rule */
        $rule = self::getContainer()->getByType(MissingCheckedExceptionInMethodThrowsRule::class);
        return $rule;
    }

    public function testRule(): void
    {
        $this->analyse(
            [__DIR__ . '/Fixtures/TestService.php'],
            [
                [
                    "Method NepadaTests\PHPStan\MessageBus\Fixtures\TestService::handleOrderCommand() throws checked exception NepadaTests\PHPStan\MessageBus\Fixtures\FailedToPlaceOrderException but it's missing from the PHPDoc @throws tag.",
                    49,
                ],
                [
                    "Method NepadaTests\PHPStan\MessageBus\Fixtures\TestService::handleOrderCommand() throws checked exception NepadaTests\PHPStan\MessageBus\Fixtures\FailedToRejectOrderException but it's missing from the PHPDoc @throws tag.",
                    49,
                ],
            ],
        );
    }

}
