<?php

// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace WooReturns;

/** What the plugin needs before it may start, checked against what the site has (pure, WordPress-free). */
final class Requirements
{
    public const MIN_WOOCOMMERCE = '9.6';

    /** @param array<string, string> $requiredPlugins plugin name => the class that proves it is active */
    public function __construct(private readonly array $requiredPlugins) {}

    /**
     * The names of required plugins that are missing, or whose WooCommerce version is too old.
     *
     * @param callable(string): bool $classExists
     * @return list<string>
     */
    public function missing(callable $classExists, ?string $wooCommerceVersion): array
    {
        $missing = [];
        foreach ($this->requiredPlugins as $name => $class) {
            if (!$classExists($class)) {
                $missing[] = $name;
            }
        }
        if (!in_array('WooCommerce', $missing, true)
            && ($wooCommerceVersion === null || version_compare($wooCommerceVersion, self::MIN_WOOCOMMERCE, '<'))) {
            $missing[] = 'WooCommerce ' . self::MIN_WOOCOMMERCE . '+';
        }

        return $missing;
    }
}
