<?php

namespace YOOtheme\Builder\Wordpress\Source\Type;

use YOOtheme\Str;
use function YOOtheme\trans;

class PostArchiveQueryType
{
    /**
     * @param \WP_Post_Type $type
     *
     * @return array
     */
    public static function config(\WP_Post_Type $type)
    {
        $name = Str::camelCase($type->name, true);
        $field = Str::camelCase(['archive', $type->name]);

        return [
            'fields' => [
                Str::camelCase([$field, 'Single']) => [
                    'type' => Str::camelCase($name, true),

                    'args' => [
                        'offset' => [
                            'type' => 'Int',
                        ],
                    ],

                    'metadata' => [
                        'label' => $type->labels->singular_name,
                        'group' => trans('Page'),
                        'view' => [
                            "archive-{$type->name}",
                            'search',
                            'author-archive',
                            'date-archive',
                        ],
                        'fields' => [
                            'offset' => [
                                'label' => trans('Start'),
                                'description' => trans(
                                    'Set the starting point to specify which %post_type% is loaded.',
                                    ['%post_type%' => $type->labels->singular_name],
                                ),
                                'type' => 'number',
                                'default' => 0,
                                'modifier' => 1,
                                'attrs' => [
                                    'min' => 1,
                                    'required' => true,
                                ],
                            ],
                        ],
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolveSingle',
                    ],
                ],
                $field => [
                    'type' => [
                        'listOf' => $name,
                    ],

                    'args' => [
                        'offset' => [
                            'type' => 'Int',
                        ],
                        'limit' => [
                            'type' => 'Int',
                        ],
                    ],

                    'metadata' => [
                        'label' => $type->label,
                        'group' => trans('Page'),
                        'view' => [
                            "archive-{$type->name}",
                            'search',
                            'author-archive',
                            'date-archive',
                        ],
                        'fields' => [
                            '_offset' => [
                                'description' => trans(
                                    'Set the starting point and limit the number of %post_type%.',
                                    ['%post_type%' => $type->label],
                                ),
                                'type' => 'grid',
                                'width' => '1-2',
                                'fields' => [
                                    'offset' => [
                                        'label' => trans('Start'),
                                        'type' => 'number',
                                        'default' => 0,
                                        'modifier' => 1,
                                        'attrs' => [
                                            'min' => 1,
                                            'required' => true,
                                        ],
                                    ],
                                    'limit' => [
                                        'label' => trans('Quantity'),
                                        'type' => 'limit',
                                        'attrs' => [
                                            'placeholder' => 'No limit',
                                            'min' => 0,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],

                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root, array $args)
    {
        global $wp_query;

        $args += [
            'offset' => 0,
            'limit' => null,
        ];

        if ($args['offset'] || $args['limit']) {
            return array_slice(
                $wp_query->posts,
                (int) $args['offset'],
                (int) $args['limit'] ?: null,
            );
        }

        return $wp_query->posts;
    }

    public static function resolveSingle($root, array $args)
    {
        global $wp_query;

        return $wp_query->posts[$args['offset'] ?? 0] ?? null;
    }
}
