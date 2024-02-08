<?php

namespace YOOtheme\Theme;

use YOOtheme\Arr;
use YOOtheme\Config;
use YOOtheme\ImageProvider;
use YOOtheme\Url;
use YOOtheme\View;
use YOOtheme\View\HtmlElement;

class ViewHelper
{
    // https://developer.mozilla.org/en-US/docs/Web/Media/Formats/Image_types
    public const REGEX_IMAGE = '#\.(avif|gif|a?png|jpe?g|svg|webp)($|\#.*)#i';

    public const REGEX_VIDEO = '#\.(mp4|m4v|ogv|webm)$#i';

    public const REGEX_VIMEO = '#(?:player\.)?vimeo\.com(?:/video)?/(\d+)#i';

    public const REGEX_YOUTUBE = '#(?:youtube(-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|shorts)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})#i';

    public const REGEX_UNSPLASH = '#images.unsplash.com/(?<id>(?:[\w-]+/)?[\w\-.]+)#i';

    /**
     * @var View
     */
    protected $view;

    /**
     * @var ImageProvider
     */
    protected $image;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param Config        $config
     * @param View          $view
     * @param ImageProvider $image
     */
    public function __construct(Config $config, View $view, ImageProvider $image)
    {
        $this->view = $view;
        $this->image = $image;
        $this->config = $config;
    }

    /**
     * Register helper.
     *
     * @param View $view
     */
    public function register($view)
    {
        // Loaders
        $view->addLoader(function ($name, $parameters, $next) {
            $content = $next($name, $parameters);

            // Apply to root template view only
            if (empty($parameters['_root'])) {
                return $content;
            }

            return $this->image->replace($content);
        });

        // Functions
        $view->addFunction('social', [$this, 'social']);
        $view->addFunction('uid', [$this, 'uid']);
        $view->addFunction('iframeVideo', [$this, 'iframeVideo']);
        $view->addFunction('isVideo', [$this, 'isVideo']);
        $view->addFunction('isImage', [$this, 'isImage']);
        $view->addFunction('image', [$this, 'image']);
        $view->addFunction('bgImage', [$this, 'bgImage']);
        $view->addFunction('parallaxOptions', [$this, 'parallaxOptions']);
        $view->addFunction('striptags', [$this, 'striptags']);
        $view->addFunction('margin', [$this, 'margin']);

        // Components
        $view['html']->addComponent('image', [$this, 'comImage']);
    }

    public function social($link)
    {
        $link = strval($link);

        if (str_starts_with($link, 'mailto:')) {
            return 'mail';
        }

        if (str_starts_with($link, 'tel:')) {
            return 'receiver';
        }

        if (preg_match('#(google|goo)\.(.+?)/maps(?>/?.+)?#i', $link)) {
            return 'location';
        }

        $link = parse_url($link, PHP_URL_HOST);
        $link = explode('.', str_replace('wa.me', 'whatsapp', $link ?? ''));

        $icons = $this->config->get('theme.social_icons', []);
        return Arr::find($icons, fn($icon) => in_array($icon, $link)) ?: 'social';
    }

    /**
     * @param string $link
     * @param array  $params
     * @param bool   $defaults
     *
     * @return false|string
     */
    public function iframeVideo($link, $params = [], $defaults = true)
    {
        $link = strval($link);
        $query = parse_url($link, PHP_URL_QUERY);

        if ($query) {
            parse_str($query, $_params);
            $params = array_merge($_params, $params);
        }

        if (preg_match(static::REGEX_VIMEO, $link, $matches)) {
            if (empty($params['controls'])) {
                $params['keyboard'] = 0;
            }

            return Url::to(
                "https://player.vimeo.com/video/{$matches[1]}",
                $defaults
                    ? array_merge(
                        [
                            'loop' => 1,
                            'autoplay' => 1,
                            'autopause' => 0,
                            'controls' => 0,
                            'title' => 0,
                            'byline' => 0,
                            'setVolume' => 0,
                        ],
                        $params,
                    )
                    : $params,
            );
        }

        if (preg_match(static::REGEX_YOUTUBE, $link, $matches)) {
            if (!empty($params['loop'])) {
                $params['playlist'] = $matches[2];
            }

            if (empty($params['controls'])) {
                $params['disablekb'] = 1;
            }

            return Url::to(
                "https://www.youtube{$matches[1]}.com/embed/{$matches[2]}",
                $defaults
                    ? array_merge(
                        [
                            'rel' => 0,
                            'loop' => 1,
                            'playlist' => $matches[2],
                            'autoplay' => 1,
                            'controls' => 0,
                            'showinfo' => 0,
                            'iv_load_policy' => 3,
                            'modestbranding' => 1,
                            'wmode' => 'transparent',
                            'playsinline' => 1,
                        ],
                        $params,
                    )
                    : $params,
            );
        }

        return false;
    }

    public function uid()
    {
        static $uid = 0;

        return $uid++;
    }

    public function isVideo($link)
    {
        return $link && preg_match(static::REGEX_VIDEO, $link, $matches) ? $matches[1] : false;
    }

    /**
     * @param string|array $url
     * @param array        $attrs
     *
     * @return string
     */
    public function image($url, array $attrs = [])
    {
        $url = (array) $url;
        $path = array_shift($url);
        $isAbsolute = $this->isAbsolute($path);
        $type = $this->isImage($path);

        if (!empty($url['thumbnail']) && $isAbsolute) {
            if (is_array($url['thumbnail'])) {
                [$width, $height] = $url['thumbnail'];
                $attrs['width'] = is_numeric($width) ? $width : null;
                $attrs['height'] = is_numeric($height) ? $height : null;
            }

            if ($attrs['width'] && $attrs['height']) {
                // use unsplash resizing?
                if (preg_match(static::REGEX_UNSPLASH, $path, $matches)) {
                    $path = "https://images.unsplash.com/{$matches['id']}?fit=crop&w={$attrs['width']}&h={$attrs['height']}";
                } else {
                    $this->addAttr(
                        $attrs,
                        'style',
                        "aspect-ratio: {$attrs['width']} / {$attrs['height']}",
                    );
                    $this->addAttr($attrs, 'class', 'uk-object-cover');
                }
            }
        }

        $attrs['src'] =
            !$isAbsolute && !in_array($type, ['gif', 'svg']) && !empty($url)
                ? parse_url($path, PHP_URL_PATH) .
                    '#' .
                    http_build_query(
                        array_map(
                            fn($value) => is_array($value) ? implode(',', $value) : $value,
                            $url,
                        ),
                        '',
                        '&',
                    )
                : $path;

        if (empty($attrs['alt'])) {
            $attrs['alt'] = true;
        }

        if ($type === 'svg' && (empty($attrs['width']) || empty($attrs['height']))) {
            [$attrs['width'], $attrs['height']] = SvgHelper::getDimensions($path, $attrs);
        }

        // Deprecated YOOtheme Pro < v2.8.0
        if (!empty($attrs['uk-img'])) {
            unset($attrs['uk-img']);
        }

        $attrs['loading'] = $attrs['loading'] ?? 'lazy' ?: 'eager';

        return "<img{$this->view->attrs($attrs)}>";
    }

    /**
     * @param string $url
     * @param array  $params
     *
     * @return array
     */
    public function bgImage($url, array $params = [])
    {
        $attrs = [];
        $isResized = $params['width'] || $params['height'];
        $type = $this->isImage($url);

        $focalPoint = $this->parseFocalPoint($params['focal_point'] ?? '');

        if (preg_match(static::REGEX_UNSPLASH, $url, $matches)) {
            $url = $this->buildUnsplashUrl(
                $matches['id'],
                $params['width'],
                $params['height'],
                $focalPoint,
            );
        } elseif ($type == 'svg' || $this->isAbsolute($url)) {
            if ($isResized && !$params['size']) {
                $this->addAttr(
                    $attrs,
                    'style',
                    sprintf(
                        'background-size: %s %s;',
                        $params['width'] ? "{$params['width']}px" : 'auto',
                        $params['height'] ? "{$params['height']}px" : 'auto',
                    ),
                );
                if ($focalPoint) {
                    $this->addAttr(
                        $attrs,
                        'style',
                        sprintf('background-position: %s;', implode(' ', $focalPoint)),
                    );
                }
            }
        } elseif ($type != 'gif') {
            $url = parse_url($url, PHP_URL_PATH) . '#srcset=1';
            $url .= '&covers=' . ((int) ($params['size'] === 'cover'));
            if ($isResized) {
                $url .=
                    '&thumbnail=' .
                    join(',', [$params['width'], $params['height'], '', ...$focalPoint]);
            }
        }

        if ($image = $this->image->create($url, false)) {
            $minWidth = 0;
            if (empty($params['size'])) {
                $img = $image->apply($image->getAttribute('params'));
                $minWidth = $img->getWidth();
                $this->addAttr(
                    $attrs,
                    'style',
                    sprintf('background-size: %spx %spx;', $img->getWidth(), $img->getHeight()),
                );
            }

            $sources = $this->image->getSources($image, $minWidth);
            $srcsetAttrs = $this->image->getSrcsetAttrs($image, 'data-', $minWidth);

            if ($sources) {
                $srcsetAttrs = array_slice($srcsetAttrs, 0, 1);
            }

            $attrs = array_merge($attrs, $srcsetAttrs, [
                'data-sources' => json_encode($sources),
            ]);
        } else {
            $attrs['data-src'][] = Url::to($url);
        }

        // use eager loading?
        if (isset($params['loading'])) {
            $attrs['loading'] = $params['loading'];
        }

        $attrs['uk-img'] = true;

        $attrs['class'] = [
            $this->view->cls(
                [
                    'uk-background-norepeat',
                    'uk-background-{size}',
                    'uk-background-{position}',
                    'uk-background-image@{visibility}',
                    'uk-background-blend-{blend_mode}',
                    'uk-background-fixed{@effect: fixed}',
                ],
                $params,
            ),
        ];

        $attrs['style'][] = $params['background']
            ? "background-color: {$params['background']};"
            : '';

        if (
            $params['effect'] == 'parallax' &&
            ($options = $this->parallaxOptions($params, '', ['bgx', 'bgy']))
        ) {
            $attrs['uk-parallax'] = $options;
        }

        return $attrs;
    }

    /**
     * @param HtmlElement $element
     * @param array       $params
     *
     * @return void
     */
    public function comImage($element, array $params = [])
    {
        $defaults = ['src' => '', 'width' => '', 'height' => ''];
        $attrs = array_merge($defaults, $element->attrs);
        $type = $this->isImage($attrs['src']);
        $isAbsolute = $this->isAbsolute($attrs['src']);

        if (empty($attrs['alt'])) {
            $attrs['alt'] = true;
        }

        if ($type !== 'svg') {
            if (!empty($attrs['thumbnail'])) {
                if (is_array($attrs['thumbnail'])) {
                    $thumbnail = $attrs['thumbnail'];
                } else {
                    $thumbnail = [$attrs['width'], $attrs['height']];
                }

                $focalPoint = $this->parseFocalPoint($attrs['focal_point'] ?? '');

                if ($isAbsolute) {
                    [$width, $height] = $thumbnail;

                    if ($width && $height) {
                        // use unsplash resizing?
                        if (preg_match(static::REGEX_UNSPLASH, $attrs['src'], $matches)) {
                            $attrs['src'] = $this->buildUnsplashUrl(
                                $matches['id'],
                                $width,
                                $height,
                                $focalPoint,
                            );
                        } else {
                            $this->addAttr($attrs, 'style', "aspect-ratio: {$width} / {$height};");
                            $this->addAttr($attrs, 'class', 'uk-object-cover');

                            if ($focalPoint) {
                                $this->addAttr(
                                    $attrs,
                                    'style',
                                    sprintf('object-position: %s;', implode(' ', $focalPoint)),
                                );
                            }
                        }
                    }
                } else {
                    $query['thumbnail'] = [...array_pad($thumbnail, 3, ''), ...$focalPoint];
                    $query['srcset'] = true;
                    $attrs['width'] = $attrs['height'] = null;
                }
            }

            if (!empty($attrs['uk-cover'])) {
                $query['covers'] = true;
            }

            if ($type !== 'gif' && !$isAbsolute && $type && !empty($query)) {
                $attrs['src'] =
                    parse_url($attrs['src'], PHP_URL_PATH) .
                    '#' .
                    http_build_query(
                        array_map(
                            fn($value) => is_array($value) ? join(',', $value) : $value,
                            $query,
                        ),
                        '',
                        '&',
                    );
            }

            unset($attrs['uk-svg']);
        } elseif (empty($attrs['width']) || empty($attrs['height'])) {
            [$attrs['width'], $attrs['height']] = SvgHelper::getDimensions($attrs['src'], $attrs);
        }

        // use lazy loading?
        $attrs['loading'] = $attrs['loading'] ?? 'lazy' ?: 'eager';

        unset($attrs['thumbnail']);
        unset($attrs['focal_point']);

        // update element
        $element->name = 'img';
        $element->attrs = $attrs;
    }

    public function isImage($link)
    {
        return $link && preg_match(static::REGEX_IMAGE, $link, $matches) ? $matches[1] : false;
    }

    public function isAbsolute($url): bool
    {
        return $url && preg_match('/^(\/|#|[a-z0-9-.]+:)/', $url);
    }

    public function parallaxOptions(
        $params,
        $prefix = '',
        $props = ['x', 'y', 'scale', 'rotate', 'opacity', 'blur', 'background']
    ) {
        $prefix = "{$prefix}parallax_";

        $filter = fn($value) => implode(
            ',',
            array_filter(explode(',', $value), fn($value) => '' !== trim($value)),
        );

        $options = [];
        foreach ($props as $prop) {
            if ($value = $filter($params["{$prefix}{$prop}"] ?? '')) {
                if ($prop == 'background') {
                    $options[] = "{$prop}-color: {$value}";
                } else {
                    $options[] = "{$prop}: {$value}";
                }
            }
        }

        if (!$options) {
            return;
        }

        $options[] = sprintf(
            'easing: %s',
            is_numeric($params["{$prefix}easing"] ?? '') ? $params["{$prefix}easing"] : 0,
        );
        $options[] = !empty($params["{$prefix}breakpoint"])
            ? "media: @{$params["{$prefix}breakpoint"]}"
            : '';
        foreach (['target', 'start', 'end'] as $prop) {
            if (!empty($params[$prefix . $prop])) {
                $options[] = "{$prop}: {$params[$prefix . $prop]}";
            }
        }
        return implode('; ', array_filter($options));
    }

    public function striptags(
        $str,
        $allowable_tags = '<div><h1><h2><h3><h4><h5><h6><p><ul><ol><li><img><svg><br><hr><span><strong><em><sup><del>'
    ): string {
        return strip_tags(strval($str), $allowable_tags);
    }

    /**
     * @param string $margin
     */
    public function margin($margin): ?string
    {
        switch ($margin) {
            case '':
                return null;
            case 'default':
                return 'uk-margin-top';
            default:
                return "uk-margin-{$margin}-top";
        }
    }

    protected function addAttr(&$attrs, $name, $value): void
    {
        if (empty($attrs[$name])) {
            $attrs[$name] = [];
        } elseif (is_string($attrs[$name])) {
            $attrs[$name] = [$attrs[$name]];
        }
        $attrs[$name][] = $value;
    }

    protected function buildUnsplashUrl($id, $width, $height, $focalPoint): string
    {
        return "https://images.unsplash.com/{$id}?" .
            http_build_query(
                [
                    'fit' => 'crop',
                    'w' => $width,
                    'h' => $height,
                    'crop' =>
                        implode(
                            ',',
                            array_filter($focalPoint, fn($point) => $point && $point !== 'center'),
                        ) ?:
                        null,
                ],
                '',
                '&',
            );
    }

    protected function parseFocalPoint(string $focalPoint): array
    {
        return array_reverse(array_filter(explode('-', $focalPoint)));
    }
}
