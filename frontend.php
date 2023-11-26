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

add_action('wp_enqueue_scripts', static function () {
    add_action('wp_head', 'yos_brx_master_css_frontend_head', 1_000_001);
}, 1_000_001);

function yos_brx_master_css_frontend_head()
{
    if (function_exists('bricks_is_builder_main')) {
        if (bricks_is_builder_main()) {
            return;
        }
    }

    $config = json_decode(get_option('yos_brx_master_css_config', json_encode(YOS_BRX_MASTER_CSS_DEFAULT_CONFIG)), true);

    $html = <<<HTML
        <link rel="preload" as="script" href="https://cdn.jsdelivr.net/npm/@master/css@{$config['version']}">
    HTML;

    if ($config['presetGlobalStyles']) {
        $html .= <<<HTML
            <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/@master/normal.css@{$config['version']}">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@master/normal.css@{$config['version']}">
        HTML;
    }

    $html .= <<<HTML
        <script>
            {$config['masterCSSConfig']}
        </script>
            
        <script src="https://cdn.jsdelivr.net/npm/@master/css@beta"></script>
    HTML;

    echo $html;
}
