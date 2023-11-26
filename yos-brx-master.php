<?php

/**
 * @wordpress-plugin
 * Plugin Name:         Yabe Open Source - Bricks Master CSS
 * Plugin URI:          https://os.yabe.land
 * Description:         Bricks builder: Master CSS integration
 * Version:             1.0.0-DEV
 * Requires at least:   6.0
 * Requires PHP:        7.4
 * Author:              Rosua
 * Author URI:          https://rosua.org
 * Donate link:         https://ko-fi.com/Q5Q75XSF7
 * Text Domain:         yos-brx-master-css
 * Domain Path:         /languages
 *
 * @package             Yabe Open Source
 * @author              Joshua Gugun Siagian <suabahasa@gmail.com>
 */

/*
 * This file is part of the Yabe Open Source package.
 *
 * (c) Joshua <suabahasa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

define('YOS_BRX_MASTER_CSS_FILE', __FILE__);

define('YOS_BRX_MASTER_CSS_DEFAULT_CONFIG', [
    'version' => 'beta',
    'presetGlobalStyles' => true,
    'masterCSSConfig' => <<<JS
        window.masterCSSConfig = {
            variables: {
                primary: '#000000'
            }
        }
    JS,
]);

register_activation_hook(__FILE__, static function () {
    $req = wp_remote_get('https://data.jsdelivr.com/v1/package/npm/@master/css');

    if (!is_wp_error($req) && 200 === $req['response']['code']) {
        $data = json_decode($req['body'], true);
        $version = $data['versions'][0];
    }

    add_option('yos_brx_master_css_config', json_encode(array_merge(YOS_BRX_MASTER_CSS_DEFAULT_CONFIG, [
        'version' => $version ?? '',
    ])));
});

require_once __DIR__ . '/api.php';
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/frontend.php';
require_once __DIR__ . '/builder.php';