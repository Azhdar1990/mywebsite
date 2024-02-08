<?php

// Config
$site = '~theme.site';
$header = '~theme.mobile.header';
$navbar = '~theme.mobile.navbar';
$dialog = '~theme.mobile.dialog';
$mobile = '~theme.mobile';

// Options
$layout = $config("$header.layout");

// Outside
$outside = $config("$site.layout") == 'boxed' && $config("$site.boxed.header_outside");

// Header
$attrs = [];
$attrs['class'][] = 'tm-header-mobile';
$attrs['class'][] = $config("$mobile.breakpoint") ? "uk-hidden@{$config("$mobile.breakpoint")}" : '';

// Navbar Container
$attrs_navbar_container = [];
$attrs_navbar_container['class'][] = 'uk-navbar-container';

// Navbar
$attrs_navbar = [

    'class' => [
        'uk-navbar',
    ],

    'uk-navbar' => array_filter([
        'container' => $config("$header.transparent") && $config("$header.blend") ? ($outside ? '.tm-page-container' : '.tm-page') : '.tm-header-mobile',
        'boundary' => '.tm-header-mobile .uk-navbar-container', // By default, it would be the navbar component's element
        // Needed for dialog dropbar
        'dropbar-transparent-mode' => str_starts_with($config("$dialog.layout"), 'dropbar') && !$config("$header.transparent") ? 'remove' : null,
    ]),

];

// Sticky
$attrs_sticky = [];
if ($sticky = $config("$navbar.sticky")) {

    if ($config("$header.transparent") && $config("$header.blend")) {
        $attrs_navbar['uk-navbar']['close-on-scroll'] = true;
    } else {
        $attrs_navbar['uk-navbar']['container'] = '.tm-header-mobile > [uk-sticky]';
    }

    $attrs_sticky = array_filter([
        'uk-sticky' => true,
        'show-on-up' => $sticky == 2,
        'animation' => $sticky == 2 ? 'uk-animation-slide-top' : '',
        'cls-active' => 'uk-navbar-sticky',
        'sel-target' => '.uk-navbar-container',
    ]);

}

$attrs_navbar['uk-navbar'] = json_encode($attrs_navbar['uk-navbar']);

// Transparent
if ($config("$header.transparent") && $config("$header.blend")) {
    $attrs['class'][] = 'uk-blend-difference';
}

if ($outside && $config("$header.transparent")) {

    $attrs_navbar_container['class'][] = "uk-navbar-transparent uk-{$config("$header.transparent")}";

    if ($config("$header.blend")) {
        $attrs['class'][] = 'uk-position-z-index-high';
    } else {
        $attrs_navbar_container['class'][] = 'uk-position-relative uk-position-z-index-high';
    }

} elseif ($outside && $config("$site.boxed.header_transparent")) {
    
    if ($sticky) {
        $attrs_sticky['cls-inactive'] = "uk-navbar-transparent uk-{$config("$site.boxed.header_transparent")}";
        $attrs_sticky['top'] = '300';
        if ($sticky == 1) {
            $attrs_sticky['animation'] = 'uk-animation-slide-top';
        }
    } else {
        $attrs_navbar_container['class'][] = "uk-navbar-transparent uk-{$config("$site.boxed.header_transparent")}";
    }

} elseif ($config("$header.transparent")) {

    $attrs['uk-header'] = true;
    $attrs['tm-header-transparent'] = $config("$header.transparent");

    if ($config("$header.blend")) {
        $attrs['class'][] = 'uk-position-z-index-high';
    } else {
        $attrs_navbar_container['class'][] = 'uk-position-relative uk-position-z-index-high';
    }

} else {

    $attrs['uk-header'] = true;

}

// Width Container
$attrs_width_container = [];
$attrs_width_container['class'][] = 'uk-container uk-container-expand';

?>

<?php if (is_active_sidebar('logo-mobile') || is_active_sidebar('navbar-mobile') || is_active_sidebar('header-mobile')) :?>

<header<?= $this->attrs($attrs) ?>>

<?php

// Horizontal layouts
if (str_starts_with($layout, 'horizontal')) :

    $attrs_width_container['class'][] = is_active_sidebar('logo-mobile') && $config("$header.logo_padding_remove") && $layout != 'horizontal-center-logo' ? 'uk-padding-remove-left' : '';

    ?>

    <?php if ($sticky) : ?>
    <div<?= $this->attrs($attrs_sticky) ?>>
    <?php endif ?>

        <div<?= $this->attrs($attrs_navbar_container) ?>>

            <div<?= $this->attrs($attrs_width_container) ?>>
                <nav<?= $this->attrs($attrs_navbar) ?>>

                    <?php if (($layout != 'horizontal-center-logo' && is_active_sidebar('logo-mobile')) || (preg_match('/^horizontal-(left|center-logo)/', $layout) && is_active_sidebar('navbar-mobile'))) : ?>
                    <div class="uk-navbar-left">

                        <?php if ($layout != 'horizontal-center-logo') : ?>
                            <?php dynamic_sidebar("logo-mobile") ?>
                        <?php endif ?>

                        <?php if (preg_match('/^horizontal-(left|center-logo)/', $layout)) : ?>
                            <?php dynamic_sidebar("navbar-mobile") ?>
                        <?php endif ?>

                    </div>
                    <?php endif ?>

                    <?php if (($layout == 'horizontal-center-logo' && is_active_sidebar('logo-mobile')) || ($layout == 'horizontal-center' && is_active_sidebar('navbar-mobile'))) : ?>
                    <div class="uk-navbar-center">

                        <?php if ($layout == 'horizontal-center-logo') : ?>
                            <?php dynamic_sidebar("logo-mobile") ?>
                        <?php endif ?>

                        <?php if ($layout == 'horizontal-center') : ?>
                            <?php dynamic_sidebar("navbar-mobile") ?>
                        <?php endif ?>

                    </div>
                    <?php endif ?>

                    <?php if (is_active_sidebar('header-mobile') || ($layout == 'horizontal-right' && is_active_sidebar('navbar-mobile'))) : ?>
                    <div class="uk-navbar-right">

                        <?php if ($layout == 'horizontal-right') : ?>
                            <?php dynamic_sidebar("navbar-mobile") ?>
                        <?php endif ?>

                        <?php dynamic_sidebar("header-mobile") ?>

                    </div>
                    <?php endif ?>

                </nav>
            </div>

        </div>

    <?php if ($sticky) : ?>
    </div>
    <?php endif ?>

<?php endif ?>

<?php

// Dialog
$attrs_dialog = [];
$attrs_dialog_push = [];

if (preg_match('/^(offcanvas|modal|dropbar)-center/', $config("$dialog.layout"))) {
    $attrs_dialog['class'][] = 'uk-margin-auto-vertical';
} else {
    $attrs_dialog['class'][] = 'uk-margin-auto-bottom';
}
$attrs_dialog_push['class'][] = 'uk-grid-margin';

$attrs_dialog['class'][] = $config("$dialog.text_center") ? 'uk-text-center' : '';
$attrs_dialog_push['class'][] = $config("$dialog.text_center") ? 'uk-text-center' : '';

// Modal
$attrs_modal = [];
$attrs_modal['class'][] = 'uk-modal-body uk-padding-large uk-margin-auto uk-flex uk-flex-column uk-box-sizing-content';
$attrs_modal['class'][] = $config("$dialog.modal.width") ? 'uk-width-' .  $config("$dialog.modal.width") : 'uk-width-auto@s';
$attrs_modal['uk-height-viewport'] = true;
$attrs_modal['uk-toggle'] = json_encode(array_filter([
    'cls' => 'uk-padding-large',
    'mode' => 'media',
    'media' => '@s',
]));

// Dropbar
if (str_starts_with($config("$dialog.layout"), 'dropbar')) {

    $attrs_dropbar = [];
    $attrs_dropbar['class'][] = 'uk-dropbar';

    if (!$config("$dialog.dropbar.animation") || $config("$dialog.dropbar.animation") == 'reveal-top') {
        $attrs_dropbar['class'][] = 'uk-dropbar-top';
    } elseif ($config("$dialog.dropbar.animation") == 'slide-left') {
        $attrs_dropbar['class'][] = 'uk-dropbar-left';
    }
    elseif ($config("$dialog.dropbar.animation") == 'slide-right') {
        $attrs_dropbar['class'][] = 'uk-dropbar-right';
    }

    $attrs_dropbar['uk-drop'] = [
        // Default
        'clsDrop' => 'uk-dropbar',
        'flip' => 'false', // Has to be a string
        'container' => $sticky ? '.tm-header-mobile > [uk-sticky]' : '.tm-header-mobile',
        'target-y' => '.tm-header-mobile .uk-navbar-container',
        // New
        'mode' => 'click',
        'target-x' => '.tm-header-mobile .uk-navbar-container',
        'stretch' => true,
        'pos' => $config("$dialog.dropbar.animation") == 'slide-right' ? "bottom-right" : "bottom-left",
        'bgScroll' => 'false', // Has to be a string
        'animation' => $config("$dialog.dropbar.animation") ?: null,
        'animateOut' => true,
        'duration' => 300,
        'toggle' => 'false', // Has to be a string
    ];

    // Behind navbar
    if ($config("$header.transparent")) {
        $attrs_dropbar['class'][] = 'uk-padding-remove-top';
        $attrs_dropbar['class'][] = $config("$header.blend") && $outside ? 'uk-position-z-index-zero' : 'uk-position-z-index';

        $attrs_dropbar['uk-drop']['inset'] = true;
        $attrs_dropbar['uk-drop']['pos'] = $config("$dialog.dropbar.animation") == 'slide-right' ? "top-right" : "top-left";

        if ($config("$header.blend")) {
            $attrs_dropbar['uk-drop']['container'] = $outside ? '.tm-page-container' : '.tm-page';
        }
    }

    $attrs_dropbar['uk-drop'] = json_encode(array_filter($attrs_dropbar['uk-drop']));

}

?>

<?php if (is_active_sidebar('dialog-mobile') || is_active_sidebar('dialog-mobile-push')) : ?>

    <?php if (str_starts_with($config("$dialog.layout"), 'offcanvas')) : ?>
    <div id="tm-dialog-mobile" uk-offcanvas="container: true; overlay: true"<?= $this->attrs($config("$dialog.offcanvas") ?: []) ?>>
        <div class="uk-offcanvas-bar uk-flex uk-flex-column">

            <?php if ($config("$dialog.close")) : ?>
            <button class="uk-offcanvas-close uk-close-large" type="button" uk-close uk-toggle="cls: uk-close-large; mode: media; media: @s"></button>
            <?php endif ?>

            <?php if ((is_active_sidebar('dialog-mobile'))) : ?>
            <div<?= $this->attrs($attrs_dialog) ?>>
                <?php dynamic_sidebar("dialog-mobile:grid-stack") ?>
            </div>
            <?php endif ?>

            <?php if (is_active_sidebar('dialog-mobile-push')) : ?>
            <div<?= $this->attrs($attrs_dialog_push) ?>>
                <?php dynamic_sidebar("dialog-mobile-push:grid-stack") ?>
            </div>
            <?php endif ?>

        </div>
    </div>
    <?php endif ?>

    <?php if (str_starts_with($config("$dialog.layout"), 'modal')) : ?>
    <div id="tm-dialog-mobile" class="uk-modal-full" uk-modal>
        <div class="uk-modal-dialog uk-flex">

            <?php if ($config("$dialog.close")) : ?>
            <button class="uk-modal-close-full uk-close-large" type="button" uk-close uk-toggle="cls: uk-modal-close-full uk-close-large uk-modal-close-default; mode: media; media: @s"></button>
            <?php endif ?>

            <div<?= $this->attrs($attrs_modal) ?>>

                <?php if ((is_active_sidebar('dialog-mobile'))) : ?>
                <div<?= $this->attrs($attrs_dialog) ?>>
                    <?php dynamic_sidebar("dialog-mobile:grid-stack") ?>
                </div>
                <?php endif ?>

                <?php if (is_active_sidebar('dialog-mobile-push')) : ?>
                <div<?= $this->attrs($attrs_dialog_push) ?>>
                    <?php dynamic_sidebar("dialog-mobile-push:grid-stack") ?>
                </div>
                <?php endif ?>

            </div>

        </div>
    </div>
    <?php endif ?>

    <?php if (str_starts_with($config("$dialog.layout"), 'dropbar')) : ?>
    <div id="tm-dialog-mobile"<?= $this->attrs($attrs_dropbar) ?>>

        <div class="tm-height-min-1-1 uk-flex uk-flex-column">

            <?php if ($config("$header.transparent")) : ?>
            <div class="uk-navbar-item uk-invisible"></div>
            <?php endif ?>

            <?php if ((is_active_sidebar('dialog-mobile'))) : ?>
            <div<?= $this->attrs($attrs_dialog) ?>>
                <?php dynamic_sidebar("dialog-mobile:grid-stack") ?>
            </div>
            <?php endif ?>

            <?php if (is_active_sidebar('dialog-mobile-push')) : ?>
            <div<?= $this->attrs($attrs_dialog_push) ?>>
                <?php dynamic_sidebar("dialog-mobile-push:grid-stack") ?>
            </div>
            <?php endif ?>

        </div>

    </div>
    <?php endif ?>

<?php endif ?>

</header>

<?php endif ?>

