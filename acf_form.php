<?php

function lostfound_acf_form_init() {

  // Check function exists.
  if( function_exists('acf_register_form') ) {
    $settings = [
      'id' => 'lostfound-form',
      'post_id' => 'new_post',
      'new_post' => [
        'post_type'   => 'lostfound',
        'post_status' => 'pending'
      ],
      'field_groups' => ['group_607100ea1bad5'],
      'form' => true,
      'updated_message' => __("Thank you for your submission!", 'acf'),
      'honeypot' => true,
    ];

    acf_register_form( $settings );
  }
}
add_action('acf/init', 'lostfound_acf_form_init');