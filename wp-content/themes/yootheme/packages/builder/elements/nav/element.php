<?php

namespace YOOtheme;

return [
    'updates' => [
        '4.0.0-beta.9' => function ($node) {
            if (Arr::get($node->props, 'nav_element') === 'nav') {
                $node->props['html_element'] = 'nav';
                unset($node->props['nav_element']);
            }
        },
    ],
];
