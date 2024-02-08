<?php

namespace YOOtheme\Theme\Wordpress\Listener;

use YOOtheme\Config;
use YOOtheme\Theme\Listener\SetFavicons;

class FilterIconUrl
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(string $url): string
    {
        if (!is_customize_preview()) {
            $icons = SetFavicons::load($this->config);
            return $icons['favicon'];
        }

        return $url;
    }
}
