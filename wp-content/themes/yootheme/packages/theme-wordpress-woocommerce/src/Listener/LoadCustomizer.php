<?php

namespace YOOtheme\Theme\Wordpress\WooCommerce\Listener;

use YOOtheme\Config;
use YOOtheme\Path;

class LoadCustomizer
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        if (class_exists('WooCommerce', false)) {
            $this->config->set('woocommerce.cartPage', (int) wc_get_page_id('cart'));
            $this->config->addFile(
                'customizer',
                Path::get('../../config/customizer.json', __DIR__),
            );
        }
    }
}
