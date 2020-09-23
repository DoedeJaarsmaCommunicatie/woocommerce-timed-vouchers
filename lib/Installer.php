<?php

namespace WooCommerceTimedVouchers;

use WooCommerceTimedVouchers\Migrations\CreateBasicTables;
use WooCommerceTimedVouchers\Models\WC_Product_Timed_Voucher;

class Installer
{
    private function __construct() {}

    public static function install(): void
    {
        if (!get_term_by('slug', WC_Product_Timed_Voucher::TYPE, 'product_type')) {
            wp_insert_term(WC_Product_Timed_Voucher::TYPE, 'product_type');
        }

        // Calls migration
        require_once WOOTV_DIR . '/database/migrations/20200822_create_basic_tables.php';
        $migration = new CreateBasicTables();
        $migration->up();
    }
}
