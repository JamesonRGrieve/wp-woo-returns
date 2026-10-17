<?php

// SPDX-License-Identifier: AGPL-3.0-or-later

declare(strict_types=1);

namespace WooReturns\Wp;

use WooReturns\Requirements;

/** Wires the plugin into WordPress once its required plugins are loaded; it does nothing without them. */
final class Plugin
{
    public const VERSION = '0.0.1';

    private const REQUIRED_PLUGINS = [
        'WooCommerce' => 'WooCommerce',
    ];

    public static function boot(string $pluginFile): void
    {
        add_action('before_woocommerce_init', static function () use ($pluginFile): void {
            if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', $pluginFile, true);
            }
        });
        add_action('plugins_loaded', self::start(...));
    }

    public static function start(): void
    {
        $version = defined('WC_VERSION') ? WC_VERSION : null;
        $missing = (new Requirements(self::REQUIRED_PLUGINS))->missing(class_exists(...), $version);
        if ($missing !== []) {
            add_action('admin_notices', static function () use ($missing): void {
                echo '<div class="notice notice-error"><p>' . esc_html(sprintf(
                    /* translators: %s: comma-separated plugin names */
                    __('%1$s needs these plugins to be active: %2$s.', 'wp-woo-returns'),
                    'WP Woo Returns',
                    implode(', ', $missing),
                )) . '</p></div>';
            });
        }
    }
}
