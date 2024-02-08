<?php

namespace YOOtheme\Builder\Wordpress\Woocommerce\Listener;

class FilterTaxonomies
{
    public function handle(array $taxonomies, $object): array
    {
        $visibility = 'product_visibility';

        if ($object === 'product') {
            $taxonomies[$visibility] = get_taxonomy($visibility);
        }

        return $taxonomies;
    }
}
