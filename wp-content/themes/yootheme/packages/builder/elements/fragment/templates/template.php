<?php

// Add elements inline css above the content to ensure css is present when rendered
if (!empty($props['css'])) {
    $css = preg_replace('/[\r\n\t\h]+/u', ' ', $props['css']);
    echo "<style class=\"uk-margin-remove-adjacent\">{$css}</style>";
}

$content = $builder->render($children);

if (!$props['root']) {
    $content = $this->el($props['html_element'] ?: 'div', [
        'class' => ['uk-panel']
    ])($props, $attrs, $content);
}

echo $content;
