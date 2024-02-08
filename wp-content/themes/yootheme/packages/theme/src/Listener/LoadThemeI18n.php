<?php

namespace YOOtheme\Theme\Listener;

use YOOtheme\Config;
use function YOOtheme\trans;

class LoadThemeI18n
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        $this->config->add('theme.data.i18n', [
            'close' => ['label' => trans('Close')],
            'totop' => ['label' => trans('Back to top')],
            'marker' => ['label' => trans('Open')],
            'navbarToggleIcon' => ['label' => trans('Open menu')],
            'paginationPrevious' => ['label' => trans('Previous page')],
            'paginationNext' => ['label' => trans('Next page')],
            'searchIcon' => [
                'toggle' => 'Open Search',
                'submit' => 'Submit Search',
            ],
            'slider' => [
                'next' => trans('Next slide'),
                'previous' => trans('Previous slide'),
                'slideX' => trans('Slide %s'),
                'slideLabel' => trans('%s of %s'),
            ],
            'slideshow' => [
                'next' => trans('Next slide'),
                'previous' => trans('Previous slide'),
                'slideX' => trans('Slide %s'),
                'slideLabel' => trans('%s of %s'),
            ],
            'lightboxPanel' => [
                'next' => trans('Next slide'),
                'previous' => trans('Previous slide'),
                'slideLabel' => trans('%s of %s'),
                'close' => trans('Close'),
            ],
        ]);
    }
}
