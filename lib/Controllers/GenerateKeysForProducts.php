<?php

namespace WooCommerceTimedVouchers\Controllers;

use WooCommerceTimedVouchers\DBO\Secret;
use WooCommerceTimedVouchers\Helpers\Rand;
use WooCommerceTimedVouchers\Models\WC_Product_Timed_Voucher;

class GenerateKeysForProducts
{
    public const T_K_SECRET = '_tv_secret';

    /**
     * @param int $order_id
     */
    public static function bootstrap($order_id)
    {
        $order = wc_get_order($order_id);

        foreach ($order->get_items() as $item) {
            if ($item->get_type() !== 'line_item') {
                continue;
            }
            $product = $item->get_product();
            if ($product->get_type() === WC_Product_Timed_Voucher::TYPE) {
                $secret = Secret::make();
                if ($secret->hasOn('order_id', $order_id)) {
                    // Secret exists. Don't create a new one.
                    return;
                }

                /** @var WC_Product_Timed_Voucher $product */
                $code = Rand::generateRandomString();

                while(!\WooCommerceTimedVouchers\is_valid_secret($code)) {
                    $code = Rand::generateRandomString();
                }

                $order->add_meta_data(static::T_K_SECRET, "{$product->get_id()}:{$code}");
                $order->save_meta_data();

                $secret->product_id = $product->get_id();
                $secret->order_id = $order_id;
                $secret->created_at = current_time('mysql');
                $secret->secret = $code;
                $secret->create();
            }
        }
    }
}
