<?php

namespace YOOtheme\Builder\Wordpress\Source\Listener;

use YOOtheme\Config;

class MatchTemplate
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle($tpl)
    {
        global $post;

        $locale = $this->getLocale($this->config->get('locale.code'));

        // Will match in following order
        // category, tag, taxonomy, posts page, single post, page, or author
        // from query variable
        $object = get_queried_object();
        $pages = get_query_var('paged') && get_query_var('paged') !== 1 ? 'except_first' : 'first';

        if ($tpl === get_index_template()) {
            return [
                'type' => 'archive-post',
                'query' => compact('pages', 'locale'),
            ];
        }

        if (is_tax() || is_category() || is_tag()) {
            return [
                'type' => "taxonomy-{$object->taxonomy}",
                'query' => [
                    'terms' => $object->term_id,
                    'terms_filter' => static::getTaxonomyTermsFilterFn(),
                    'pages' => $pages,
                    'locale' => $locale,
                ],
            ];
        }

        if (is_post_type_archive()) {
            return [
                'type' => "archive-{$object->name}",
                'query' => compact('pages', 'locale'),
            ];
        }

        if (is_author()) {
            return [
                'type' => 'author-archive',
                'query' => compact('pages', 'locale'),
            ];
        }

        if (is_date()) {
            return [
                'type' => 'date-archive',
                'query' => [
                    'archive' => is_time()
                        ? 'time'
                        : (is_day()
                            ? 'day'
                            : (is_month()
                                ? 'month'
                                : 'year')),
                    'pages' => $pages,
                    'locale' => $locale,
                ],
            ];
        }

        if (is_search()) {
            return [
                'type' => 'search',
                'query' => compact('pages', 'locale'),
            ];
        }

        if (is_404()) {
            return [
                'type' => 'error-404',
                'query' => compact('locale'),
            ];
        }

        // For WooCommerce's shop page `is_page() => true` if assigned as homepage
        if (is_page() || is_single()) {
            return [
                'type' => "single-{$post->post_type}",
                'query' => !post_password_required($post)
                    ? [
                        'terms' => $this->getSingleTermsFilterFn($post),
                        'locale' => $locale,
                    ]
                    : fn() => false,
            ];
        }
    }

    protected function getLocale($locale)
    {
        if (str_contains($locale, '_')) {
            // Fallback to language code, if e.g. WPML changed the locale
            $locale = [$locale, substr($locale, 0, strpos($locale, '_'))];
        }

        return $locale;
    }

    protected function getSingleTermsFilterFn($post): \Closure
    {
        $pageTermIds = array_column(
            wp_get_object_terms($post->ID, get_object_taxonomies($post)),
            'term_id',
        );

        return function ($tmplTermIds) use ($pageTermIds) {
            if (!$tmplTermIds) {
                return true;
            }

            if (!array_intersect($pageTermIds, $tmplTermIds)) {
                return false;
            }

            $terms = get_terms(['include' => $tmplTermIds, 'hide_empty' => false]);
            $taxonomies = array_unique(array_column($terms, 'taxonomy'));
            foreach ($terms as $term) {
                if (in_array($term->term_id, $pageTermIds)) {
                    unset($taxonomies[array_search($term->taxonomy, $taxonomies)]);
                }
            }
            return empty($taxonomies);
        };
    }

    protected function getTaxonomyTermsFilterFn(): \Closure
    {
        return function ($terms) {
            global $wp_query;

            if (empty($wp_query->tax_query->queried_terms)) {
                return false;
            }

            foreach ($terms as $id) {
                $term = get_term($id);

                if (!$term) {
                    continue;
                }

                $queried = $wp_query->tax_query->queried_terms[$term->taxonomy] ?? null;
                foreach ($queried['terms'] ?? [] as $t) {
                    $t = get_term_by($queried['field'], $t, $term->taxonomy);

                    if ($t && $t->term_id === $term->term_id) {
                        return true;
                    }
                }
            }
            return false;
        };
    }
}
