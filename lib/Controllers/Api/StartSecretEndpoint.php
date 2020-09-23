<?php

namespace WooCommerceTimedVouchers\Controllers\Api;

use WP_REST_Request;
use WP_REST_Response;
use WooCommerceTimedVouchers\DBO\Secret;

class StartSecretEndpoint extends RestEndpoint
{
    public const F_K_START_ROUTE_R = 'TimedVouchers/Rest/start-route/data';

    public function __construct()
    {
        register_rest_route(
            $this->namespace,
            $this->generate_url('route', 'start'),
            [
                'methods' => ['GET', 'POST'],
                'callback' => [$this, 'startRoute'],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function startRoute(WP_REST_Request $request): WP_REST_Response
    {
        if (!$request->get_param('secret')) {
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

        $secret->start_routes();

        $data= apply_filters(
            static::F_K_START_ROUTE_R,
            [
                'valid' => true,
                'valid_until' => $secret->valid_until,
                'secret' => $secret->secret,
            ],
            $secret
        );

        return new WP_REST_Response($data);
    }
}
