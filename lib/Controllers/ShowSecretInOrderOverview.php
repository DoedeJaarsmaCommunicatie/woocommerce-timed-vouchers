<?php

namespace WooCommerceTimedVouchers\Controllers;

use WC_Product;
use WC_Order_Item;
use Carbon\Carbon;
use WC_Order_Item_Product;
use WooCommerceTimedVouchers\DBO\Secret;
use WooCommerceTimedVouchers\Helpers\View;
use WooCommerceTimedVouchers\Models\WC_Product_Timed_Voucher;

class ShowSecretInOrderOverview
{
    /**
     * Adds the secret per order item.
     *
     * @param int|string                           $item_id
     * @param WC_Order_Item_Product|WC_Order_Item  $item
     * @param WC_Product_Timed_Voucher|WC_Product  $product
     */
    public static function bootstrap($item_id, $item, $product)
    {
        if ($product->get_type() !== WC_Product_Timed_Voucher::TYPE) {
            return;
        }

        $secrets = Secret::make()
              ->findManyOn('order_id', $item->get_order_id());

        foreach ($secrets as $secret) {
            if ($product->get_id() === (int) $secret->product_id) {
                print View::render(
                    'secret-order-meta',
                    apply_filters('TimedVouchers/Views/secret-order-data/variables', ['secret' => $secret])
                );
            }
        }
    }
}
