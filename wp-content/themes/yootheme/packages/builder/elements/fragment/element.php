<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function ($node, $params) {
            $node->props['root'] = !$params['parent'];
        },
    ],
];
