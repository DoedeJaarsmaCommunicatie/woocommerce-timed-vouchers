<?php

namespace WooCommerceTimedVouchers;

final class Updater
{
    private static $_is_booted = false;

    /**
     * @var null|self
     */
    private static $_instance = null;

    private function __construct()
    {
        \Puc_v4_Factory::buildUpdateChecker(
            'https://github.com/DoedeJaarsmaCommunicatie/woocommerce-timed-vouchers/',
            WOOTV_FILE,
            'woocommerce-timed-vouchers'
        );

        static::$_is_booted = true;
    }

    public static function bootstrap(): self
    {
        if (static::is_booted()) {
            return static::$_instance;
        }

        return static::$_instance = new static();
    }

    public static function is_booted(): bool
    {
        return null !== static::$_instance && static::$_is_booted;
    }
}
