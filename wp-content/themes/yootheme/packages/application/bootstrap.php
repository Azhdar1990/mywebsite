<?php

namespace YOOtheme\Application;

use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Url;
use YOOtheme\View;

return [
    'config' => function (Config $config) {
        $config->addFilter(
            'url',
            fn($value, $file) => Url::to(Path::resolve(dirname($file), $value)),
        );
    },

    'extend' => [
        View::class => function (View $view, $app) {
            $view->addGlobal('app', $app);
            $view->addGlobal('config', $app(Config::class));
        },
    ],

    'aliases' => [
        View::class => 'view',
    ],

    'loaders' => [
        'services' => ServiceLoader::class,
        'aliases' => AliasLoader::class,
        'extend' => ExtendLoader::class,
        'events' => EventLoader::class,
        'routes' => RouteLoader::class,
        'config' => ConfigLoader::class,
    ],
];
