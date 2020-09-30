<?php

namespace WooCommerceTimedVouchers;

use WooCommerceTimedVouchers\Filters\AddNewProductType;
use WooCommerceTimedVouchers\Controllers\ShowSecretInOrderMail;
use WooCommerceTimedVouchers\Controllers\GenerateKeysForProducts;
use WooCommerceTimedVouchers\Controllers\Api\StartSecretEndpoint;
use WooCommerceTimedVouchers\Controllers\Api\SecretDataController;
use WooCommerceTimedVouchers\Controllers\ShowSecretInOrderOverview;
use WooCommerceTimedVouchers\Controllers\Api\ValidateSecretEndpoint;

class TimedVouchers
{
    const PRE_INIT_ACTION = 'TimedVouchers/init/pre';
    const INIT_ACTION = 'TimedVouchers/init';
    const BOOT_ACTION = 'TimbedVouchers/booted';

    /**
     * @var null|TimedVouchers
     */
    protected static $_instance = null;

    protected static $_is_booted = false;

    private function __construct()
    {
        add_filter('product_type_selector', [AddNewProductType::class, 'add_type']);
        add_filter('woocommerce_product_class', [AddNewProductType::class, 'fix_class_loading'], 10, 2);
        add_action('woocommerce_timed-voucher_add_to_cart', function() {
            do_action( 'woocommerce_simple_add_to_cart' );
        });

        Updater::bootstrap();

        $this->load_secrets_in_data();
        $this->add_rest_routes();
        $this->load_assets();
    }

    public static function bootstrap(): self
    {
        if (null !== static::$_instance) {
            return static::$_instance;
        }

        do_action(static::PRE_INIT_ACTION);
        static::$_instance = new static();
        do_action(static::INIT_ACTION, static::$_instance, static::$_is_booted);
        static::$_is_booted = true;
        do_action(static::BOOT_ACTION, static::$_instance, static::$_is_booted);

        return static::$_instance;
    }

    private function load_secrets_in_data()
    {
        // Adds the secrets to the db to use in REST api.
        add_action('woocommerce_order_status_processing', [GenerateKeysForProducts::class, 'bootstrap']);
        // Show the secrets in the woocommerce admin.
        add_action('woocommerce_after_order_itemmeta', [ShowSecretInOrderOverview::class, 'bootstrap'], 10, 3);
        // Show the secrets in the woocommerce mail.
        add_action('woocommerce_order_item_meta_end', [ShowSecretInOrderMail::class, 'bootstrap'], 10, 4);
    }

    private function load_assets(): void
    {
        $is_debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

        add_action('admin_footer', static function () use ($is_debug) {
            wp_enqueue_script(
                'woo-tv-main',
                plugin_dir_url(WOOTV_FILE) . 'dist/' . ($is_debug ? 'main.iife.js' : 'main.iife.min.js'),
                [],
                filemtime(plugin_dir_path(WOOTV_FILE) . '/dist/' . ($is_debug ? 'main.iife.js' : 'main.iife.min.js'))
            );
        });
    }

    private function add_rest_routes()
    {
        add_action('rest_api_init', [ValidateSecretEndpoint::class, 'bootstrap']);
        add_action('rest_api_init', [StartSecretEndpoint::class, 'bootstrap']);
        add_action('rest_api_init', [SecretDataController::class, 'bootstrap']);
    }
}
