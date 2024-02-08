<ul class="uk-pagination uk-margin-large uk-flex-center">
    <?php foreach ($links as $link): ?>
    <li<?= strpos($link, 'current') ? ' class="uk-active"' : '' ?>><?= $link ?></li>
    <?php endforeach ?>
</ul>
