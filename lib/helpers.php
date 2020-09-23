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
