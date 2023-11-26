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

add_action('wp_enqueue_scripts', 'yos_brx_master_css_builder_enqueue_scripts', 1_000_001);

function yos_brx_master_css_builder_enqueue_scripts()
{
    if (!function_exists('bricks_is_builder_main') || !bricks_is_builder_main()) {
        return;
    }

    add_filter('script_loader_tag', function ($tag, $handle) {
        if ('yos-brx-master-css-builder' !== $handle) {
            return $tag;
        }

        return str_replace(' src', ' type="module" src', $tag);
    }, 1_000_001, 2);

    wp_enqueue_script(
        'yos-brx-master-css-builder',
        plugins_url('builder.js', __FILE__),
        ['wp-hooks', 'bricks-builder',],
        (string) filemtime(__DIR__ . '/builder.js'),
        true
    );
}