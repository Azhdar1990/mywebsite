<?php

namespace YOOtheme\Theme\Wordpress\Listener;

use YOOtheme\Config;
use YOOtheme\Path;
use YOOtheme\Wordpress\Update;

class LoadThemeUpdate
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle()
    {
        Update::register(
            Path::basename('~theme'),
            'theme',
            $this->config->get('theme.update', ''),
            [
                'key' => $this->config->get('~theme.yootheme_apikey'),
                'stability' => $this->config->get('~theme.minimum_stability'),
            ],
        );

        // @link https://developer.wordpress.org/reference/hooks/upgrader_pre_install/
        add_filter(
            'upgrader_pre_install',
            function ($return, $package) {
                if (!is_wp_error($return)) {
                    static::move($package);
                }

                return $return;
            },
            10,
            2,
        );

        // @link https://developer.wordpress.org/reference/hooks/upgrader_post_install/
        add_filter(
            'upgrader_post_install',
            function ($return, $package) {
                if (!is_wp_error($return)) {
                    static::move($package, true);
                }

                return $return;
            },
            10,
            2,
        );
    }

    protected static function move($package, $reverse = false)
    {
        /** @var \WP_Filesystem_Base $wp_filesystem */
        global $wp_filesystem;

        $themeDir = Path::get('~theme');
        $name = $package['theme'] ?? '';
        $content = $wp_filesystem->wp_content_dir();

        if ($name != basename($themeDir)) {
            return;
        }

        $paths = [$themeDir, "{$content}/upgrade"];

        [$source, $target] = $reverse ? array_reverse($paths) : $paths;

        $files = array_merge(glob("{$source}/fonts/*"), glob("{$source}/css/theme*.css"));

        foreach ($files as $file) {
            // skip theme.update.css
            if (strpos($file, 'update.css')) {
                continue;
            }

            $filename = ltrim(substr($file, strlen($source)), '\\/');
            $directory = dirname("{$target}/{$filename}");

            if (!$wp_filesystem->is_dir($directory)) {
                $wp_filesystem->mkdir($directory);
            }

            $wp_filesystem->move($file, "{$target}/{$filename}", true);
        }
    }
}
