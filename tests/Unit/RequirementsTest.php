<?php

// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace WooReturns\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooReturns\Requirements;

final class RequirementsTest extends TestCase
{
    private const PLUGINS = ['WooCommerce' => 'WooCommerce', 'Polylang' => 'Polylang'];

    public function testNothingMissingWhenEveryPluginIsActiveAndCurrent(): void
    {
        self::assertSame([], (new Requirements(self::PLUGINS))->missing(static fn(string $class): bool => true, '10.2.1'));
    }

    public function testNamesEachInactivePlugin(): void
    {
        $active = static fn(string $class): bool => $class === 'WooCommerce';

        self::assertSame(['Polylang'], (new Requirements(self::PLUGINS))->missing($active, '9.6'));
    }

    public function testAnOldOrUnknownWooCommerceVersionIsMissing(): void
    {
        $requirements = new Requirements(['WooCommerce' => 'WooCommerce']);
        $all = static fn(string $class): bool => true;

        self::assertSame(['WooCommerce 9.6+'], $requirements->missing($all, '9.5.9'));
        self::assertSame(['WooCommerce 9.6+'], $requirements->missing($all, null));
    }

    public function testAnInactiveWooCommerceIsNamedOnce(): void
    {
        $none = static fn(string $class): bool => false;

        self::assertSame(['WooCommerce'], (new Requirements(['WooCommerce' => 'WooCommerce']))->missing($none, null));
    }
}
