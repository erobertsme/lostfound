<?php
add_action('acf/init', 'lostfound_add_acf_fields');
function lostfound_add_acf_fields() {

  acf_add_local_field_group([
    'key' => 'group_607100ea1bad5',
    'title' => 'Lost and Found Pets Fields',
    'fields' => [
      [
        'key' => 'field_6071010a63a70',
        'label' => 'Lost/Found',
        'name' => 'lost_found',
        'type' => 'radio',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'choices' => [
          'Lost' => 'Lost',
          'Found' => 'Found',
        ],
        'allow_null' => 0,
        'other_choice' => 0,
        'default_value' => 'Lost',
        'layout' => 'horizontal',
        'return_format' => 'value',
        'save_other_choice' => 0,
      ],
      [
        'key' => 'field_6071013f63a71',
        'label' => 'Location',
        'name' => 'location',
        'type' => 'text',
        'instructions' => 'Location where the pet was found',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => '',
      ],
      [
        'key' => 'field_6071016363a72',
        'label' => 'Date Lost/Found',
        'name' => 'date',
        'type' => 'date_picker',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'display_format' => 'F j, Y',
        'return_format' => 'Ymd',
        'first_day' => 0,
      ],
      [
        'key' => 'field_60710a6663a73',
        'label' => 'Type of Pet',
        'name' => 'pet_type',
        'type' => 'select',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'taxonomy' => 'pet-type',
        'allow_terms' => '',
        'allow_level' => '',
        'field_type' => 'select',
        'default_value' => [
        ],
        'return_format' => 'id',
        'ui' => 0,
        'allow_null' => 0,
        'multiple' => 0,
        'save_terms' => 1,
        'load_terms' => 1,
        'choices' => [
        ],
        'ajax' => 0,
        'placeholder' => '',
        'layout' => '',
        'toggle' => 0,
        'allow_custom' => 0,
        'other_choice' => 0,
      ],
      [
        'key' => 'field_60710abf63a74',
        'label' => 'Name',
        'name' => 'name',
        'type' => 'text',
        'instructions' => 'Your name',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => '',
      ],
      [
        'key' => 'field_60710ad063a75',
        'label' => 'Phone',
        'name' => 'phone',
        'type' => 'text',
        'instructions' => 'Your Phone Number',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
        'maxlength' => '',
      ],
      [
        'key' => 'field_60710b0763a76',
        'label' => 'Email',
        'name' => 'email',
        'type' => 'email',
        'instructions' => 'Your Email',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'default_value' => '',
        'placeholder' => '',
        'prepend' => '',
        'append' => '',
      ],
      [
        'key' => 'field_60710b1663a77',
        'label' => 'Description',
        'name' => 'description',
        'type' => 'textarea',
        'instructions' => '',
        'required' => 1,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'default_value' => '',
        'placeholder' => '',
        'maxlength' => '',
        'rows' => '',
        'new_lines' => '',
      ],
      [
        'key' => 'field_60710b2c63a78',
        'label' => 'Photo',
        'name' => 'photo',
        'type' => 'file',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => [
          'width' => '',
          'class' => '',
          'id' => '',
        ],
        'uploader' => '',
        'return_format' => 'array',
        'min_size' => '',
        'max_size' => '',
        'mime_types' => 'jpg,jpeg,png,gif',
        'library' => 'all',
      ],
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'lostfound',
        ],
      ],
    ],
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'left',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
  ]);
  
}