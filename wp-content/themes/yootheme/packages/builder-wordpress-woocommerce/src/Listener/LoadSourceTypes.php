<?php

namespace YOOtheme\Builder\Wordpress\Woocommerce\Listener;

use YOOtheme\Builder\Wordpress\Source\Type\TaxonomyQueryType;
use YOOtheme\Builder\Wordpress\Source\Type\TaxonomyType;
use YOOtheme\Builder\Wordpress\Woocommerce\Type;
use YOOtheme\Str;

class LoadSourceTypes
{
    public function handle($source): void
    {
        $source->objectType('Product', Type\ProductType::config());
        $source->objectType('ProductCat', Type\ProductCategoryType::config());
        $source->objectType('ProductsQuery', Type\CustomProductQueryType::config());
        $source->objectType('AttributeField', Type\AttributeFieldType::config());
        $source->objectType('WoocommerceFields', Type\FieldsType::config());

        $this->renameLabel($source, 'ProductTagsQuery', 'customProductTag', 'Custom Product tag');
        $this->renameLabel(
            $source,
            'ProductCatsQuery',
            'customProductCat',
            'Custom Product category',
        );

        foreach ($this->getAttributeTaxonomies() as $taxonomy) {
            $source->queryType(TaxonomyQueryType::config($source, $taxonomy, false));
            $source->objectType(
                Str::camelCase($taxonomy->name, true),
                TaxonomyType::config($taxonomy),
            );
        }

        $source->objectType('Site', Type\SiteType::config($source));
    }

    protected function renameLabel($source, $name, $field, $label): void
    {
        $source->objectType($name, [
            'fields' => [
                $field => ['metadata' => compact('label')],
            ],
        ]);
    }

    protected function getAttributeTaxonomies(): array
    {
        $taxonomies = [];

        foreach (wc_get_attribute_taxonomy_names() as $name) {
            $taxonomy = get_taxonomy($name);

            if ($taxonomy && $taxonomy->public) {
                $taxonomies[$name] = $taxonomy;
            }
        }

        return $taxonomies;
    }
}
