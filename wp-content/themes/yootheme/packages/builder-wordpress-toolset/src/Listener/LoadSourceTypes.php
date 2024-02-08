<?php

namespace YOOtheme\Builder\Wordpress\Toolset\Listener;

use YOOtheme\Builder\Wordpress\Source\Helper as SourceHelper;
use YOOtheme\Builder\Wordpress\Toolset\Helper;
use YOOtheme\Builder\Wordpress\Toolset\Type;
use YOOtheme\Str;
use function YOOtheme\trans;

class LoadSourceTypes
{
    public function handle($source): void
    {
        if (!Helper::isActive()) {
            return;
        }

        $source->objectType('ToolsetValueField', Type\ValueType::config());
        $source->objectType('ToolsetDateField', Type\ValueType::configDate());

        if (class_exists(\Toolset_Addon_Maps_Common::class)) {
            $source->objectType('ToolsetMapsField', Type\MapsFieldType::config());
        }

        // add user fields
        if ($fields = Helper::fieldsGroups('users')) {
            $this->configFields($source, 'User', $fields);
        }

        // add post fields
        foreach (SourceHelper::getPostTypes() as $type) {
            if ($fields = Helper::fieldsGroups('posts', $type->name)) {
                $this->configFields($source, $type->name, $fields);
            }

            if ($relationships = Helper::getRelationships($type->name)) {
                $this->configRelationshipFields($source, $type->name, $relationships);
            }
        }

        // add taxonomy fields
        foreach (SourceHelper::getTaxonomies() as $taxonomy) {
            if ($fields = Helper::fieldsGroups('terms', $taxonomy->name)) {
                $this->configFields($source, $taxonomy->name, $fields);
            }
        }
    }

    protected function configFields($source, string $name, array $fields): void
    {
        $type = Str::camelCase([$name, 'Toolset'], true);

        // add field on type
        $source->objectType(Str::camelCase($name, true), [
            'fields' => [
                'toolset' => [
                    'type' => $type,
                    'metadata' => [
                        'label' => trans('Fields'),
                    ],
                    'extensions' => [
                        'call' => Type\FieldsType::class . '::toolset',
                    ],
                ],
            ],
        ]);

        $source->objectType($type, Type\FieldsType::config($source, $fields));
    }

    protected function configRelationshipFields($source, string $name, array $relationships): void
    {
        $type = Str::camelCase([$name, 'Toolset'], true);

        // add field on type
        $source->objectType(Str::camelCase($name, true), [
            'fields' => [
                'toolset' => [
                    'type' => $type,
                    'metadata' => [
                        'label' => trans('Fields'),
                    ],
                    'extensions' => [
                        'call' => Type\FieldsType::class . '::toolset',
                    ],
                ],
            ],
        ]);

        $source->objectType(
            $type,
            Type\RelationshipFieldsType::config($source, $name, $relationships),
        );
    }
}
