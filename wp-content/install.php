<?php

function wp_install_defaults()
{
    global $wpdb, $wp_rewrite;

    $example = __DIR__ . '/sample_yootheme.json';
    $queries = json_decode(file_get_contents($example));
    $replace = [
        '@@SITE_URL@@' => get_option('siteurl'),
        '@@ADMIN_EMAIL@@' => get_option('admin_email'),
        '@@TABLE_PREFIX@@' => $wpdb->prefix,
    ];

    // run insert queries
    foreach ($queries as $query) {
        $wpdb->query(strtr($query, $replace));
    }

    // trigger thumbnail generation
    if (file_exists(__DIR__ . '/plugins/woocommerce/woocommerce.php')) {
        include_once __DIR__ . '/plugins/woocommerce/woocommerce.php';

        WC_Regenerate_Images::init();
        WC_Regenerate_Images::queue_image_regeneration();
    }

    $wp_rewrite->flush_rules();
    wp_cache_flush();

    unlink($example);
    unlink(__FILE__);
}
