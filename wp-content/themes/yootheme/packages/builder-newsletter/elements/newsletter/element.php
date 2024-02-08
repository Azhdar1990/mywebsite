<?php

namespace YOOtheme;

use YOOtheme\Builder\Newsletter\NewsletterController;

return [
    'transforms' => [
        'render' => function ($node) {
            /**
             * @var NewsletterController $controller
             * @var Metadata $meta
             */
            [$controller, $meta] = app(NewsletterController::class, Metadata::class);

            $provider = (array) $node->props['provider'];

            $node->settings = $controller->encodeData(
                array_merge($provider, (array) $node->props[$provider['name']]),
            );
            $node->form = [
                'action' => Url::route('theme/newsletter/subscribe', [
                    'hash' => $controller->getHash($node->settings),
                ]),
            ];

            $meta->set('script:newsletter', [
                'src' => Path::get('../../app/newsletter.min.js', __DIR__),
                'defer' => true,
            ]);
        },
    ],

    'updates' => [
        '1.22.0-beta.0.1' => function ($node) {
            Arr::updateKeys($node->props, ['gutter' => 'gap']);
        },

        '1.20.0-beta.1.1' => function ($node) {
            Arr::updateKeys($node->props, ['maxwidth_align' => 'block_align']);
        },
    ],
];
