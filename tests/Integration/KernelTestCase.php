<?php

declare(strict_types=1);

namespace BlueGrid\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as SymfonyWebTestCase;

abstract class KernelTestCase extends SymfonyWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function hashmapEquals(array $expected, array $actual, $message = ''): void
    {
        self::assertIsArray($expected);
        self::assertIsArray($actual);

        self::assertCount(
            \count($expected),
            $actual,
            \trim(
                \sprintf(
                    'Actual array is not same size as expected. Missing keys %s%s',
                    \implode(',', \array_diff(\array_keys($actual), \array_keys($expected))),
                    \implode(',', \array_diff(\array_keys($expected), \array_keys($actual))),
                ),
            )
        );

        foreach ($expected as $key => $value) {
            if (is_array($value)) {
                self::assertArrayHasKey($key, $actual, \trim(\sprintf('%s Missing key: "%s"', $message, $key)));
                self::hashmapEquals($value, $actual[$key], $message);
            } else {
                self::assertContains($value, $actual, \sprintf('%s Element not found: "%s"', $message, $value));
            }
        }
    }
}
