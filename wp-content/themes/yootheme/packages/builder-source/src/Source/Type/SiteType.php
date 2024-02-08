<?php

namespace YOOtheme\Builder\Source\Type;

use YOOtheme\Config;
use YOOtheme\Http\Request;
use function YOOtheme\app;
use function YOOtheme\trans;

class SiteType
{
    /**
     * @return array
     */
    public static function config()
    {
        return [
            'fields' => [
                'title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Site Title'),
                        'filters' => ['limit'],
                    ],
                ],

                'page_title' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Page Title'),
                        'filters' => ['limit'],
                    ],
                ],

                'page_locale' => [
                    'type' => 'String',
                    'metadata' => [
                        'label' => trans('Page Locale'),
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolvePageLocale',
                    ],
                ],

                'page_url' => [
                    'type' => 'String',
                    'args' => [
                        'query' => [
                            'type' => 'Boolean',
                        ],
                    ],
                    'metadata' => [
                        'label' => trans('Page URL'),
                        'arguments' => [
                            'query' => [
                                'label' => trans('Query String'),
                                'type' => 'checkbox',
                                'text' => trans('Include query string'),
                                'default' => false,
                            ],
                        ],
                    ],
                    'extensions' => [
                        'call' => __CLASS__ . '::resolvePageUrl',
                    ],
                ],

                'user' => [
                    'type' => 'User',
                    'metadata' => [
                        'label' => trans('Current User'),
                    ],
                ],

                'is_guest' => [
                    'type' => 'Int',
                    'metadata' => [
                        'label' => trans('Guest User'),
                        'condition' => true,
                    ],
                ],
            ],

            'metadata' => [
                'type' => true,
                'label' => trans('Site'),
            ],
        ];
    }

    public static function resolvePageLocale()
    {
        return app(Config::class)('locale.code');
    }

    public static function resolvePageUrl($obj, array $args)
    {
        $uri = app(Request::class)->getUri();
        return $uri->getPath() . ($args['query'] ? "?{$uri->getQuery()}" : '');
    }
}
