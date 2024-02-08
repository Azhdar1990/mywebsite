<?php

namespace YOOtheme\Builder\Source\Listener;

class OrderSourceMetadata
{
    public static function handle($metadata)
    {
        if (!empty($metadata['arguments'])) {
            $metadata['arguments'] = static::applyOrder($metadata['arguments']);
        }

        if (!empty($metadata['fields'])) {
            $metadata['fields'] = static::applyOrder($metadata['fields']);
        }

        return $metadata;
    }

    protected static function applyOrder(array $fields): array
    {
        uasort(
            $fields,
            fn($fieldA, $fieldB) => ($fieldA['@order'] ?? 0) - ($fieldB['@order'] ?? 0),
        );

        return $fields;
    }
}
