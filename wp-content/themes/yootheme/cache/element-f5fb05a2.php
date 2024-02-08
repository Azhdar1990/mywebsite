<?php // $file = C:/xampp/htdocs/wordpress/wp-content/themes/yootheme/packages/builder/elements/layout/element.json

return [
  '@import' => $filter->apply('path', './element.php', $file), 
  'name' => 'layout', 
  'title' => 'Layout', 
  'container' => true, 
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file), 
    'content' => $filter->apply('path', './templates/content.php', $file)
  ]
];
