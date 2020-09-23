<?php

namespace WooCommerceTimedVouchers\Controllers\Api;

use WooCommerceTimedVouchers\DBO\Secret;

class ValidateSecretEndpoint extends RestEndpoint
{
    public function __construct()
    {
        register_rest_route(
            $this->namespace,
            $this->generate_url('secret', 'validate'),
            [
                'methods' => 'GET',
                'callback' => [$this, 'validateEndpoint'],
                'permission_callback' => '__return_true'
            ]
        );
    }

    public function validateEndpoint(\WP_REST_Request $request)
    {
        if (!$request->has_param('secret')) {
            wp_send_json_error(
                [
                    'error' => '400',
                    'message' => 'No secret passed.',
                ]
            );
        }

        $secret = $request->get_param('secret');
        $secret = Secret::make()->findOn('secret', $secret);

        if (!$secret || !$secret->is_valid()) {
            wp_send_json_error(
                [
                    'error' => '404',
                    'message' => 'Secret incorrect.',
                ]
            );
        }

        $response = new \WP_REST_Response(
            [
                'valid' => true,
                'valid_until' => $secret->valid_until,
            ]
        );

        return $response;

    }
}
