<?php

namespace YOOtheme\Theme\Wordpress\WPML;

use YOOtheme\Theme\Wordpress\MenuConfig;

return [
    'events' => [
        MenuConfig::class => [Listener\LoadMenuConfig::class => 'handle'],
        'customizer.init' => [Listener\LoadBuilderConfig::class => ['handle', 10]],
        'url.resolve' => [Listener\AddLanguageParameter::class => 'handle'],
    ],
];
