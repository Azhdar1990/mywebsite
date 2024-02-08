<?php

namespace YOOtheme;

return [
    'transforms' => [
        'render' => function () {
            if (!is_product()) {
                return false;
            }

            global $product;

            if (
                $product->get_type() === 'simple' &&
                (!$product->is_purchasable() || !$product->is_in_stock())
            ) {
                return false;
            }
        },
    ],
];
