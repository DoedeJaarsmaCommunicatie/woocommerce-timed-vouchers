<?php

namespace WooCommerceTimedVouchers\Controllers;

use WC_Order;
use WC_Order_Item;
use WC_Order_Item_Product;
use WooCommerceTimedVouchers\DBO\Secret;
use WooCommerceTimedVouchers\Helpers\View;

class ShowSecretInOrderMail
{
    /**
     * @param int|string                          $item_id The order item id
     * @param WC_Order_Item_Product|WC_Order_Item $item
     * @param WC_Order                            $order
     * @param bool                                $plain_text If the mail is plain text.
     */
    public static function bootstrap($item_id, $item, $order, $plain_text): void
    {
        if ($plain_text) {
            // Render plain text.
            static::print_plain_text($item_id, $item, $order);
            return;
        }

        static::print_meta_data($item_id, $item, $order);
    }

    /**
     * @param int|string                          $item_id The order item id
     * @param WC_Order_Item_Product|WC_Order_Item $item
     * @param WC_Order                            $order
     */
    final private static function print_meta_data($item_id, $item, $order): void
    {
        $product = $item->get_product();

        $secrets = Secret::make()->findManyOn('order_id', $order->get_id());
        foreach ($secrets as $secret) {
            if ($product->get_id() === (int) $secret->product_id) {
                print View::render(
                    'secret-mail-meta',
                    apply_filters('TimedVouchers/Views/secret-mail-data/variables', ['secret' => $secret], $order)
                );
            }
        }
    }

    /**
     * @param int|string                          $item_id The order item id
     * @param WC_Order_Item_Product|WC_Order_Item $item
     * @param WC_Order                            $order
     */
    final private static function print_plain_text($item_id, $item, $order): void
    {
        $product = $item->get_product();

        print '';
    }
}
