<?php
declare(strict_types = 1);

namespace NepadaTests\PHPStan\MessageBus;

use PHPStan\Analyser\Error;
use PHPStan\Rules\Exceptions\MissingCheckedExceptionInMethodThrowsRule;
use PHPStan\Rules\Rule;

/**
 * @extends DynamicExtensionsRuleTestCase<MissingCheckedExceptionInMethodThrowsRule>
 */
class MissingCheckedExceptionInMethodThrowsRuleTest extends DynamicExtensionsRuleTestCase
{

    protected function getRule(): Rule
    {
        return self::getContainer()->getByType(MissingCheckedExceptionInMethodThrowsRule::class);
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

    /**
     * @param string[] $files
     * @return list<Error>
     */
    public function gatherAnalyserErrors(array $files): array
    {
        // Workaround for unstable order of found errors
        $errors = parent::gatherAnalyserErrors($files);
        usort(
            $errors,
            fn (Error $a, Error $b): int => $a->getMessage() <=> $b->getMessage(),
        );
        return $errors;
    }

}
