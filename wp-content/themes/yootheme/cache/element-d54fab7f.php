<?php // $file = C:/xampp/htdocs/wordpress/wp-content/themes/yootheme/packages/builder/elements/button_item/element.json

return [
  '@import' => $filter->apply('path', './element.php', $file), 
  'name' => 'button_item', 
  'title' => 'Button', 
  'width' => 500, 
  'defaults' => [
    'button_style' => 'default', 
    'icon_align' => 'left'
  ], 
  'placeholder' => [
    'props' => [
      'content' => 'Button', 
      'link' => '#', 
      'icon' => ''
    ]
  ], 
  'templates' => [
    'render' => $filter->apply('path', './templates/template.php', $file), 
    'content' => $filter->apply('path', './templates/content.php', $file)
  ], 
  'fields' => [
    'content' => [
      'label' => 'Content', 
      'source' => true
    ], 
    'link' => $config->get('builder.link'), 
    'link_title' => $config->get('builder.link_title'), 
    'link_aria_label' => $config->get('builder.link_aria_label'), 
    'icon' => [
      'label' => 'Icon', 
      'description' => 'Pick an optional icon from the icon library.', 
      'type' => 'icon', 
      'source' => true
    ], 
    'button_style' => [
      'label' => 'Style', 
      'description' => 'Set the button style.', 
      'type' => 'select', 
      'options' => [
        'Default' => 'default', 
        'Primary' => 'primary', 
        'Secondary' => 'secondary', 
        'Danger' => 'danger', 
        'Text' => 'text', 
        'Link' => '', 
        'Link Muted' => 'link-muted', 
        'Link Text' => 'link-text'
      ]
    ], 
    'link_target' => [
      'label' => 'Target', 
      'type' => 'select', 
      'options' => [
        'Same Window' => '', 
        'New Window' => 'blank', 
        'Modal' => 'modal'
      ]
    ], 
    'lightbox_width' => [
      'attrs' => [
        'placeholder' => 'auto'
      ], 
      'enable' => 'link_target == \'modal\''
    ], 
    'lightbox_height' => [
      'attrs' => [
        'placeholder' => 'auto'
      ], 
      'enable' => 'link_target == \'modal\''
    ], 
    'lightbox_image_focal_point' => [
      'label' => 'Modal Focal Point', 
      'description' => 'Set a focal point to adjust the image focus when cropping.', 
      'type' => 'select', 
      'options' => [
        'Top Left' => 'top-left', 
        'Top Center' => 'top-center', 
        'Top Right' => 'top-right', 
        'Center Left' => 'center-left', 
        'Center Center' => '', 
        'Center Right' => 'center-right', 
        'Bottom Left' => 'bottom-left', 
        'Bottom Center' => 'bottom-center', 
        'Bottom Right' => 'bottom-right'
      ], 
      'source' => true, 
      'enable' => 'link_target == \'modal\''
    ], 
    'icon_align' => [
      'label' => 'Alignment', 
      'description' => 'Choose the icon position.', 
      'type' => 'select', 
      'options' => [
        'Left' => 'left', 
        'Right' => 'right'
      ], 
      'enable' => 'icon'
    ], 
    'status' => $config->get('builder.statusItem'), 
    'source' => $config->get('builder.source')
  ], 
  'fieldset' => [
    'default' => [
      'type' => 'tabs', 
      'fields' => [[
          'title' => 'Content', 
          'fields' => ['content', 'link', 'link_title', 'link_aria_label', 'icon']
        ], [
          'title' => 'Settings', 
          'fields' => [[
              'label' => 'Button', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['button_style']
            ], [
              'label' => 'Link', 
              'type' => 'group', 
              'divider' => true, 
              'fields' => ['link_target', [
                  'label' => 'Modal Width/Height', 
                  'description' => 'Set the width and height for the modal content, i.e. image, video or iframe.', 
                  'type' => 'grid', 
                  'width' => '1-2', 
                  'fields' => ['lightbox_width', 'lightbox_height']
                ], 'lightbox_image_focal_point']
            ], [
              'label' => 'Icon', 
              'type' => 'group', 
              'fields' => ['icon_align']
            ]]
        ], $config->get('builder.advancedItem')]
    ]
  ]
];
