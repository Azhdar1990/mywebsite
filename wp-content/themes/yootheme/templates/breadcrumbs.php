<?php
    $el = $this->el('nav', [
        'class' => 'uk-margin-medium-bottom',
        'aria-label' => 'Breadcrumb',
    ]);

    $list = $this->el('ul', [
        'class' => ['uk-breadcrumb'],
        'vocab' => 'https://schema.org/',
        'typeof' => 'BreadcrumbList',
    ]);

    $li = $this->el('li', [
        'property' => 'itemListElement',
        'typeof' => 'ListItem',
    ]);

    $span = $this->el('span', ['property' => 'name']);

    $position = 1;
?>

<?php if ($items) : ?>

<?= $el() ?>

    <?= $list() ?>

    <?php foreach ($items as $key => $item) : ?>

    <?php if (!empty($item->link)) : ?>
        <?= $li() ?>
            <a href="<?= $item->link ?>" property="item" typeof="WebPage"><?= $span([], $item->name) ?></a>
            <meta property="position" content="<?= $position++ ?>">
    <?php elseif ($key !== array_key_last($items)) : ?>
        <li class="uk-disabled">
            <a><span><?= $item->name ?></span></a>
    <?php else : ?>
        <?= $li() ?>
            <?= $span([], $item->name) ?>
            <meta property="position" content="<?= $position++ ?>">
    <?php endif ?>
        <?= $li->end() ?>
    <?php endforeach ?>

    <?= $list->end() ?>

<?= $el->end() ?>

<?php endif ?>
