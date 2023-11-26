<?php

/*
 * This file is part of the Yabe Open Source package.
 *
 * (c) Joshua <suabahasa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

add_action('rest_api_init', 'yos_brx_master_css_rest_api_init');
function yos_brx_master_css_rest_api_init()
{
    register_rest_route(
        'yos-brx-master-css/v1',
        '/index',
        [
            'methods' => 'GET',
            'callback' => 'yos_brx_master_css_rest_api_index',
            'permission_callback' => function ($wprestRequest) {
                return wp_verify_nonce($wprestRequest->get_header('X-WP-Nonce'), 'wp_rest') && current_user_can('manage_options');
            },
        ]
    );

    register_rest_route(
        'yos-brx-master-css/v1',
        '/store',
        [
            'methods' => 'POST',
            'callback' => 'yos_brx_master_css_rest_api_store',
            'permission_callback' => function ($wprestRequest) {
                return wp_verify_nonce($wprestRequest->get_header('X-WP-Nonce'), 'wp_rest') && current_user_can('manage_options');
            },
        ]
    );
}

function yos_brx_master_css_rest_api_index(WP_REST_Request $request)
{
    $config = get_option('yos_brx_master_css_config', YOS_BRX_MASTER_CSS_DEFAULT_CONFIG);

    return new WP_REST_Response(json_decode($config, false), 200);
}

function yos_brx_master_css_rest_api_store(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    update_option('yos_brx_master_css_config', json_encode($data));

    return new WP_REST_Response(null, 200);
}
