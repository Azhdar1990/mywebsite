<?php

use YOOtheme\Arr;

$fields = ['image', 'title', 'meta', 'content', 'link'];

// Find empty fields
$filtered = array_values(Arr::filter($fields, fn($field) =>
    Arr::some($children, fn($child) =>
        $child->props[$field] != ''
    )
));

?>

<?php if (count($children)) : ?>
<table>
    <?php if (Arr::some($filtered, fn($field) => $props["table_head_{$field}"])) : ?>
    <thead>
        <tr>

            <?php foreach ($filtered as $field) : ?>
            <th><?= $props["table_head_{$field}"] ?></th>
            <?php endforeach ?>

        </tr>
    </thead>
    <?php endif ?>

    <tbody>
        <?php foreach ($children as $child) : ?>
        <tr><?= $builder->render($child, ['element' => $props, 'filtered' => $filtered]) ?></tr>
        <?php endforeach ?>
    </tbody>

</table>
<?php endif ?>
