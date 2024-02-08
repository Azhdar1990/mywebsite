<?php

namespace YOOtheme\Theme\Wordpress\WooCommerce\Listener;

use YOOtheme\Config;

class ShowCartQuantity
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Add fragment with cart item count.
     */
    public static function addToCartFragments(array $fragments): array
    {
        return $fragments + static::getCartQuantity();
    }

    /**
     * Filters the navigation menu items being returned.
     */
    public function navMenuObjects(array $items): array
    {
        if (!\WC()->cart) {
            return $items;
        }

        foreach ($items as $item) {
            if ($item->object === 'page' && ((int) $item->object_id) === wc_get_page_id('cart')) {
                $quantities = static::getCartQuantity();
                $quantity =
                    $this->config->get(
                        "~theme.menu.items.{$item->ID}.woocommerce_cart_quantity",
                    ) === 'badge'
                        ? $quantities['[data-cart-badge]']
                        : $quantities['[data-cart-brackets]'];

                $this->config->set("~theme.menu.items.{$item->ID}.title-suffix", $quantity);
            }
        }

        return $items;
    }

    protected static function getCartQuantity(): array
    {
        $quantity = \WC()->cart->get_cart_contents_count();

        return [
            '[data-cart-badge]' => sprintf(
                '<span%s data-cart-badge>%s</span>',
                $quantity ? ' class="uk-badge"' : '',
                $quantity ?: '',
            ),
            '[data-cart-brackets]' =>
                '<span data-cart-brackets>' . ($quantity ? "({$quantity})" : '') . '</span>',
        ];
    }
}
