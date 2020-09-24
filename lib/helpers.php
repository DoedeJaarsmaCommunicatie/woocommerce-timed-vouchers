<?php

namespace WooCommerceTimedVouchers;

use WooCommerceTimedVouchers\DBO\Secret;
use WooCommerceTimedVouchers\Helpers\Rand;

if (!function_exists(__NAMESPACE__ . 'rand_string')) {
    function rand_string($length = 10)
    {
        return Rand::generateRandomString($length);
    }
}

if (!function_exists(__NAMESPACE__ . 'is_valid_secret')) {
    function is_valid_secret(string $secret)
    {
        if (Secret::make()->find($secret)) {
            return false;
        }

        return true;
    }
}

if (!function_exists(__NAMESPACE__ . 'get_secret_order')) {
    function get_secret_order(string $secret) {
        if (!is_valid_secret($secret)) {
            return false;
        }

        $model = Secret::make()->find($secret);

        return wc_get_order($model->order_id);
    }
}

if (!function_exists(__NAMESPACE__ . 'generate_secret_for_order')) {
    /**
     * @param int|null $order_id
     * @param int $product_id
     *
     * @return Secret
     */
    function generate_secret_for_order(?int $order_id, int $product_id) {
        if (!$order_id) {
            $order_id = -1;
        }

        $secret = Secret::make(
            [
                'order_id' => $order_id,
                'product_id' => $product_id,
                'created_at' => current_time('mysql'),
            ]
        );

        $code = Rand::generateRandomString();
        while (!is_valid_secret($code)) {
            $code = Rand::generateRandomString();
        }

        $secret->secret = $code;

        $secret->create();

        return $secret;
    }
}

if (!function_exists(__NAMESPACE__ . 'get_secrets_by_product')) {
    /**
     * @param int|string $product_id
     * @param bool       $valid_only
     *
     * @return array|Secret[]
     */
    function get_secrets_by_product($product_id, bool $valid_only = true)
    {
        $secrets = Secret::make()
                         ->findManyOn('product_id', $product_id);

        $secrets = array_filter($secrets, static function (Secret $secret) {
            return !$secret->has_order();
        });

        if ($valid_only) { // Only valid secrets
            $secrets = array_filter($secrets, static function (Secret $secret) {
                return $secret->is_valid();
            });
        }

        return $secrets;
    }
}

if (!function_exists(__NAMESPACE__ . 'get_global_secrets')) {
    function get_global_secrets(bool $valid_only = true)
    {
        $secrets = Secret::make()
            ->all();


        $secrets = array_filter($secrets, static function (Secret $secret) {
            return $secret->order_id === -1;
        });

        if ($valid_only) {
            $secrets = array_filter($secrets, static function (Secret $secret) {
                return $secret->order_id === -1;
            });
        }

        return $secrets;
    }
}
