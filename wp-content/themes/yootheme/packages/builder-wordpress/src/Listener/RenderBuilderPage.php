<?php

namespace YOOtheme\Builder\Wordpress\Listener;

use YOOtheme\Builder;
use YOOtheme\Builder\Wordpress\PostHelper;
use YOOtheme\Config;
use YOOtheme\View;

class RenderBuilderPage
{
    public View $view;
    public Config $config;
    public Builder $builder;

    public function __construct(Config $config, Builder $builder, View $view)
    {
        $this->view = $view;
        $this->config = $config;
        $this->builder = $builder;
    }

    public function handle($template)
    {
        if (!is_page() && !is_single()) {
            return $template;
        }

        global $post;

        if (post_password_required($post)) {
            return $template;
        }

        $content = isset($post->post_content)
            ? PostHelper::matchContent($post->post_content)
            : false;

        if ($this->config->get('app.isCustomizer')) {
            if ($page = $this->config->get('req.customizer.page')) {
                if ($post->ID === $page['id']) {
                    $content = !empty($page['content']) ? json_encode($page['content']) : null;
                } else {
                    unset($page);
                }
            }

            $modified = !empty($page);

            $this->config->add('customizer.page', [
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $content ? $this->builder->load($content) : $content,
                'modified' => $modified,
                'collision' => PostHelper::getCollision($post),
            ]);
        }

        // Render builder output
        if ($content) {
            // Delay rendering to ensure regular WordPress content flow (wp_enqueue_scripts hook before shortcode hooks)
            $this->view['sections']->set(
                'builder',
                fn() => $this->builder->render($content, ['prefix' => 'page', 'post' => $post]),
            );
        }

        return $template;
    }
}
