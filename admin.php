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

add_action('admin_menu', 'yos_brx_master_css_add_admin_menu', 1_000_001);

function yos_brx_master_css_add_admin_menu()
{
    $hook = add_submenu_page(
        'bricks',
        'Yabe Open Source - Bricks Master CSS',
        'Master CSS',
        'manage_options',
        'yos_brx_master_css',
        'yos_brx_master_css_render',
        1_000_001
    );

    add_action('load-' . $hook, static function () {
        add_action('admin_enqueue_scripts', 'yos_brx_master_css_enqueue_scripts', 1_000_001);
    } );
}

function yos_brx_master_css_render()
{
    echo '<div id="yos-brx-master-css-app" class="pt:20"></div>';
}

function yos_brx_master_css_enqueue_scripts()
{
    add_filter('script_loader_tag', function ($tag, $handle) {
        if ('yos-brx-master-css-admin' !== $handle) {
            return $tag;
        }

        return str_replace(' src', ' type="module" src', $tag);
    }, 1_000_001, 2);

    wp_enqueue_script(
        'yos-brx-master-css-admin',
        plugins_url('admin.js', __FILE__),
        [],
        (string) filemtime(__DIR__ . '/admin.js'),
        true
    );

    // get plugin data
    $plugin_data = get_plugin_data(YOS_BRX_MASTER_CSS_FILE);

    wp_localize_script('yos-brx-master-css-admin', 'yosBrxMasterCSS', [
        '_version' => $plugin_data['Version'],
        'rest_api' => [
            'nonce' => wp_create_nonce('wp_rest'),
            'root' => esc_url_raw(rest_url()),
            'namespace' => 'yos-brx-master-css/v1',
            'url' => esc_url_raw(rest_url('yos-brx-master-css/v1')),
        ],
    ]);
}




if (is_admin()) {
    add_filter('plugin_action_links_' . plugin_basename(YOS_BRX_MASTER_CSS_FILE), static function ($links) {
        array_unshift($links, sprintf(
            '<a href="%s">%s</a>',
            add_query_arg(['page' => 'yos_brx_master_css'], admin_url('admin.php')),
            'Settings'
        ));

        return $links;
    });
}