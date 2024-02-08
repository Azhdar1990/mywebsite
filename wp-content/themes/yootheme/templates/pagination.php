<?php
/**
 * Pagination links for archive post pages.
 *
 * @link https://developer.wordpress.org/reference/functions/paginate_links/
 */

namespace YOOtheme;

$config = app(Config::class);

global $wp_query;

$args = [
    'type' => 'array',
    'mid_size' => 3,
    'end_size' => 1,
    'next_text' => '<span uk-pagination-next></span>',
    'prev_text' => '<span uk-pagination-previous></span>',
];

?>

<?php if ($wp_query->max_num_pages > 1) : ?>

    <?php if ($config('~theme.blog.navigation') == 'pagination') : ?>
    <ul class="uk-pagination uk-margin-large uk-flex-center">
        <?php foreach (paginate_links($args) as $link) : ?>
        <li<?= strpos($link, 'current') ? ' class="uk-active"' : '' ?>><?= $link ?></li>
        <?php endforeach ?>
    </ul>
    <?php endif ?>

    <?php if ($config('~theme.blog.navigation') == 'previous/next') : ?>
    <ul class="uk-pagination uk-margin-large">
        <?php if ($prev = get_previous_posts_link(strtr(__('&laquo; Previous'), ['&laquo;' => '<span uk-pagination-previous></span>']))) : ?>
        <li><?= $prev ?></li>
        <?php endif ?>
        <?php if ($next = get_next_posts_link(strtr(__('Next &raquo;'), ['&raquo;' => '<span uk-pagination-next></span>']))) : ?>
        <li class="uk-margin-auto-left"><?= $next ?></li>
        <?php endif ?>
    </ul>
    <?php endif ?>

<?php endif ?>
