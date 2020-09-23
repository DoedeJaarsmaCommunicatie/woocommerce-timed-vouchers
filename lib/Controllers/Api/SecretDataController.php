<?php

namespace WooCommerceTimedVouchers\Controllers\Api;

use WP_REST_Request;
use WP_REST_Response;
use WooCommerceTimedVouchers\DBO\Secret;

class SecretDataController extends RestEndpoint
{
    public const F_K_SECRET_PRODUCT_R = 'TimedVouchers/Rest/secret-product/data';

    public function __construct()
    {
        register_rest_route(
            $this->namespace,
            $this->generate_url('secret', 'product'),
            [
                'methods' => ['GET'],
                'callback' => [$this, 'get_secret_product'],
                'permission_callback' => '__return_true'
            ]
        );
    }

    public function get_secret_product(WP_REST_Request $request): WP_REST_Response
    {
        if (!$request->has_param('secret')) {
            wp_send_json_error(
                [
                    'error' => '400',
                    'message' => 'No secret passed.',
                ], 400
            );
        }

        $secret = Secret::make()->findOn('secret', $request->get_param('secret'));

        if (!$secret || !$secret->is_valid()) {
            wp_send_json_error(
                [
                    'error' => '404',
                    'message' => 'Secret incorrect.',
                ], 404
            );
        }

        $product = wc_get_product($secret->product_id);

        $data = apply_filters(
            static::F_K_SECRET_PRODUCT_R,
            [
                'product_id' => $product->get_id(),
                'product' => (array) $product,
            ],
            $product
        );

        return new WP_REST_Response($data);
    }
}
