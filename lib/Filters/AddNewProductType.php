<?php

namespace WooCommerceTimedVouchers\Filters;

use WooCommerceTimedVouchers\Models\WC_Product_Timed_Voucher;

class AddNewProductType
{
    public static function add_type($types)
    {
        if (!isset($types[WC_Product_Timed_Voucher::TYPE])) {
            $types[WC_Product_Timed_Voucher::TYPE] = 'Timed Voucher';
        }

        return $types;
    }

    public static function fix_class_loading(string $className, string $product_type)
    {
        if ($product_type === WC_Product_Timed_Voucher::TYPE) {
            return WC_Product_Timed_Voucher::class;
        }

        return $className;
    }
}
