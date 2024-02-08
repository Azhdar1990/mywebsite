<?php

// Initialize prop if not set
$props += [
    'media_overlay_gradient' => null,
    'background_parallax_background' => null,
];

// Resets
if (str_starts_with($props['style'] ?? '', 'card-')) {
    if ($props['padding'] == 'xsmall') {
        $props['padding'] = 'small';
    }
    if ($props['padding'] == 'xlarge') {
        $props['padding'] = 'large';
    }
}

if ($props['image']) { $props['video'] = false; }

// New logic shortcuts
$props['has_panel'] = $props['style'] || $props['background_color'] || $props['background_parallax_background'] || $props['image'] || $props['video'] || in_array($props['position_sticky'], ['row', 'section']) || $props['row_height'];
$props['has_overlay'] = ($props['image'] || $props['video']) && ($props['media_overlay'] || $props['media_overlay_gradient']);

// Cell
$el = $this->el($props['html_element'] ?: 'div', [

    'class' => [

        // Match child height
        'uk-grid-item-match' => ($props['vertical_align'] || $props['style'] || $props['background_color'] || $props['background_parallax_background'] || $props['image'] || $props['video'] || !empty($props['element_absolute'])) && !in_array($props['position_sticky'], ['row', 'section']) && !$props['row_height'],

        // Vertical alignment
        'uk-flex-{vertical_align} {@!has_panel}',

        // Sticky
        'js-sticky {@position_sticky: column} {@!has_panel}',

        // Text color
        'uk-{text_color}' => !$props['style'] || $props['image'] || $props['video'],

        // Breakpoint widths
        'uk-width-1-1 {@!width_default} {@!width_small} {@!width_medium} {@!width_large} {@!width_xlarge}', // Make sure at least one `uk-width` is set
        'uk-width-{width_default}',
        'uk-width-{width_small}@s',
        'uk-width-{width_medium}@m',
        'uk-width-{width_large}@l',
        'uk-width-{width_xlarge}@xl',

        // Order first
        'uk-flex-first {@order_first: xs}',
        'uk-flex-first@{order_first} {@!order_first: xs}',

    ],

    // Row parallax
    'style' => [
        'align-self: stretch {@row_parallax} {@position_sticky}',
    ],


]);

// Panel: Tile / Background / Image / Video / Sticky / Row Height
$panel = $props['has_panel'] ? $this->el('div', [

    'class' => [
        'uk-{style} [uk-card {@style: card-.*}] [uk-card-{!padding: |none} {@style: card-.*}]',

        // Match child height
        'uk-flex {@image}',

        // Clip video
        'uk-cover-container {@video} {@!image}',

        // Preserve color
        'uk-preserve-color {@style: (card|tile)-(primary|secondary)}' => $props['preserve_color'] || ($props['text_color'] && ($props['image'] || $props['video'])),
    ],

    // Video Background
    'style' => [
        'background-color: {media_background};{@video}',
        'background-color: {background_color};{@!media_background}{@!video}{@!style}'
    ],

    // Height Viewport
    'uk-height-viewport' => [
        'offset-top: true; {@row_height} {@!position_sticky: row|section}',
        'offset-top: !*; {@row_height} {@position_sticky: row|section}',
        'offset-bottom: 20; {@row_height: percent}',
    ],

]) : null;

if (!$props['style'] && $bgParallax = $this->parallaxOptions($props, 'background_', ['background'])) {
    $panel->attr('uk-parallax', $bgParallax);
}

if (in_array($props['position_sticky'], ['row', 'section'])) {

    $panel->attr([

        'class' => [
            'uk-position-z-index', // Force sticky below sticky navbar
        ],

        'uk-sticky' => $this->expr([
            'offset: {position_sticky_offset};',
            'end: !.tm-grid-expand; {@position_sticky: row}',
            'end: !.uk-section; {@position_sticky: section}',
            'media: @{position_sticky_breakpoint};',
        ], $props) ?: true,

    ]);

}

// Image
$image = $props['image'] ? $this->el('div', $this->bgImage($props['image'], [
    'width' => $props['image_width'],
    'height' => $props['image_height'],
    'focal_point' => $props['image_focal_point'],
    'loading' => $props['image_loading'] ? 'eager' : null,
    'size' => $props['image_size'],
    'position' => $props['image_position'],
    'visibility' => $props['media_visibility'],
    'blend_mode' => $props['media_blend_mode'],
    'background' => $props['media_background'],
    'effect' => $props['image_effect'],
    'parallax_bgx' => $props['image_parallax_bgx'],
    'parallax_bgy' => $props['image_parallax_bgy'],
    'parallax_easing' => $props['image_parallax_easing'],
    'parallax_breakpoint' => $props['image_parallax_breakpoint'],
])) : null;

// Video
$video = $props['video'] ? include "{$__dir}/template-video.php" : null;

// Overlay
$overlay = $props['has_overlay'] ? $this->el('div', [

    'class' => ['uk-position-cover'],

    'style' => [
        'background-color: {media_overlay};',
        // `background-clip` fixes sub-pixel issue
        'background-image: {media_overlay_gradient}; background-clip: padding-box',
    ],

]) : null;

if ($props['style'] || $props['background_color'] || $props['background_parallax_background'] || $props['image'] || $props['video']) {

    ($props['image'] ? $image : $panel)->attr('class', [
        // Padding and position context for the overlay
        'uk-tile {@!style: card-.*} [uk-tile-{!padding: |none}]',
        'uk-card-body {@style: card-.*}',

        // Needed if height matches parent
        'uk-width-1-1 {@image}',

        // Padding
        'uk-padding-remove {@padding: none}',

        // Position Context for Image Overlay
        'uk-position-relative {@image} {@!style}',

        // Match child height
        // 'uk-flex {@has_overlay}',
    ]);

} elseif (in_array($props['position_sticky'], ['row', 'section'])) {

    $panel->attr('class', [
        // Remove child margin
        'uk-panel',
    ]);

}

if ($props['has_panel']) {

    ($props['image'] ? $image : $panel)->attr('class', [

        'js-sticky {@position_sticky: column}',

        // Vertical alignment
        'uk-flex uk-flex-{vertical_align}',

    ]);
}

// Align elements as block.
// Make sure overlay is always below content.
// Add position context considering any tile padding.
$container = $props['vertical_align'] || $props['has_overlay'] || !empty($props['element_absolute']) ? $this->el('div', [

    'class' => [
        'uk-panel uk-width-1-1',
    ],

]) : null;

// Sticky
$sticky = $props['position_sticky'] == 'column' ? $this->el('div', [

    'class' => [
        'uk-panel uk-position-z-index', // Force sticky below sticky navbar
    ],

    'uk-sticky' => $this->expr([
        'offset: {position_sticky_offset};',
        'end: !.js-sticky;', // Selects panel, so tile padding is considered
        'media: @{position_sticky_breakpoint};',
    ], $props) ?: true,

]) : null;

?>

<?= $el($props, $attrs) ?>

    <?php if ($panel) : ?>
    <?= $panel($props) ?>
    <?php endif ?>

        <?php if ($image) : ?>
        <?= $image($props) ?>
        <?php endif ?>

            <?php if ($video) : ?>
            <?= $video($props, '') ?>
            <?php endif ?>

            <?php if ($overlay) : ?>
            <?= $overlay($props, '') ?>
            <?php endif ?>

            <?php if ($container) : ?>
            <?= $container($props) ?>
            <?php endif ?>

                <?php if ($sticky) : ?>
                <?= $sticky($props) ?>
                <?php endif ?>

                    <?= $builder->render($children) ?>

                <?php if ($sticky) : ?>
                <?= $sticky->end() ?>
                <?php endif ?>

            <?php if ($container) : ?>
            <?= $container->end() ?>
            <?php endif ?>

        <?php if ($image) : ?>
        <?= $image->end() ?>
        <?php endif ?>

    <?php if ($panel) : ?>
    <?= $panel->end() ?>
    <?php endif ?>

<?= $el->end() ?>
