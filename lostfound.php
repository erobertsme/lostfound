<?php
/**
 * Plugin Name: Lost and Found
 * Version: 1.4.4
 * Description: Creates a shortcode to display a form which allows users to submit to a Lost and Found custom post type with custom fields and a custom Taxonomy. Use <strong>[lostfound_form]</strong> to display the form.
 * Plugin URI: https://github.com/omfgtora/lostfound
 * Author: Ethan Roberts
 * License: GPL v3 or later
 * Licence URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: lostfound
 */

if( !defined( 'ABSPATH' ) || !class_exists( 'LostFound') ) return;

Class LostFound {

  private static $instance = null;

  public function __construct() {
    // Define path and URL to the ACF plugin.
    define( 'LOSTFOUND_ACF_PATH', plugin_dir_path( __FILE__ ) . 'includes/acf/' );
    define( 'LOSTFOUND_ACF_URL', plugin_dir_url( __FILE__ ) . 'includes/acf/' );

    add_action( 'init', [$this, 'initialize'], 0, 0 );
    add_action( 'get_header', [$this, 'load_acf_form_head'], 0, 0 );
    add_action( 'acf/submit_form', [$this, 'action_send_email'], 10, 2);
    add_shortcode( 'lostfound_form', [$this, 'register_form_shortcode'] );
    add_shortcode( 'lostfound_date', [$this, 'register_date_shortcode'] );
    add_shortcode( 'lostfound_pet_type', [$this, 'register_pet_type_shortcode'] );
    add_filter('acf/update_value/name=photo', [$this, 'acf_set_featured_image'], 10, 3);
    //add_action( 'wp_head', [$this, 'zerospam_load_key']);
  }

  public static function instance() {
    if( isset( self::$instance) ) return self::$instance;

    return self::$instance = new LostFound();
  }

  public function initialize() {
    // Bail early if called directly from functions.php or plugin file.
    if( !did_action( 'plugins_loaded' ) ) return;

    $this->include_acf();
    $this->register_cpt_tax();

    require_once( plugin_dir_path( __FILE__ ) . 'lostfound_options.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'acf_fields.php' );

    $this->create_defaults();

    register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
  }

  public static function deactivate() {
    delete_option( 'lostfound_terms_created' );
    delete_option( 'lostfound_settings' );
    unregister_setting( 'lostfound_settings_group', 'lostfound_settings' );
    // delete_option( 'lostfound_zerospam_key' );
  }

  // Register Post type and Taxonomy
  private function register_cpt_tax() {
    register_post_type( 'lostfound', [
      'labels' => [
        'name' => __( 'Lost and Found Pets', 'lostfound' ),
        'singular_name' => __( 'Lost and Found Pet', 'lostfound' ),
      ],
      'public' => true,
      'has_archive' => true,
      'menu_icon' => 'dashicons-pets',
      'rewrite' => ['slug' => 'lostfound'],
      'supports' => [
        'title',
        'editor',
        'author',
        'thumbnail',
        'custom-fields',
        'pet-type',
        'comments',
      ],
      'taxonomies' => ['pet-type', 'post_tag'],
    ]);

    register_taxonomy( 'pet-type', 'lostfound', [
      'labels' => [
        'name' => __( 'Pet types', 'lostfound' ),
        'singular_name' => __( 'Pet type', 'lostfound' ),
      ],
      'rewrite' => ['slug' => 'pet-type'],
      'show_admin_column' => true,
      'supports' => [
        'title',
        'editor',
        'author',
      ]
    ]);
  }

  public function output_options_page() {
    load_template( plugin_dir_path( __FILE__ ) . 'lostfound_options.php' );
  }

  private function create_defaults() {
    if ( get_option( 'lostfound_terms_created' ) ) return;

    wp_insert_term( 'Cat', 'pet-type' );
    wp_insert_term( 'Dog', 'pet-type' );
    wp_insert_term( 'Other', 'pet-type' );

    update_option( 'lostfound_settings', [
      'new_post_status' => 'pending',
      'notifications_email' => get_bloginfo( 'admin_email' ),
      'submit_redirect_url' => ''
    ]);

    update_option( 'lostfound_terms_created', true );
  }

  private function include_acf() {
    // Include the ACF plugin.
    include_once( LOSTFOUND_ACF_PATH . 'acf.php' );

    // Customize the url setting to fix incorrect asset URLs.
    add_filter('acf/settings/url', function( $url ) {
      return LOSTFOUND_ACF_URL;
    });

    // Hide the ACF admin menu item.
    add_filter( 'acf/settings/show_admin', '__return_false' );
  }

  public function load_acf_form_head() {    
    if( is_page() && !has_shortcode( get_post( get_the_ID() )->post_content, "lostfound_form" ) ) return;

    acf_form_head();
  }

  public function register_form_shortcode( $atts ) {
    // wp_enqueue_script( 'lostfound_zerospam', plugin_dir_url( __FILE__ ) . 'includes/js/zerospam.js', [], NULL, true );
    // add_action( 'wp_enqueue_scripts', 'lostfound_zerospam' );
    
    if ( is_admin() ) return;

    $atts = shortcode_atts( [], $atts, 'lostfound_form' );

    $new_post_status = get_option( 'lostfound_settings' )['new_post_status'];

    $settings = [
      'id' => 'lostfound-form',
      'post_id' => 'new_post',
      'new_post' => [
        'post_type'   => 'lostfound',
        'post_status' => ( $new_post_status ) ? $new_post_status : 'pending',
      ],
      'post_title' => true,
      'field_groups' => ['lostfound-form-groups'],
      'form' => true,
      'updated_message' => __( "Thank you for your submission!", 'acf' ),
      'honeypot' => true,
      'submit_value' => __( "Submit", 'acf' ),
      'return' => get_option( 'lostfound_settings' )['submit_redirect_url'],
    ];

    return acf_form( $settings );
  }

  public function register_date_shortcode() {

    if ( get_post_type() !== 'lostfound' ) return;

    return get_field( 'field_6071016363a72' );
  }

  public function register_pet_type_shortcode() {

    if ( get_post_type() !== 'lostfound' ) return;

    return get_field( 'field_60710a6663a73' )->name;
  }

  function acf_set_featured_image( $value, $post_id, $field  ){
    
    if($value != ''){
      //Add the value which is the image ID to the _thumbnail_id meta data for the current post
      add_post_meta($post_id, '_thumbnail_id', $value);
    }

    return $value;
}

  private function send_email_notification( $settings ) {
    if ( empty( $settings['email'] ) ) $settings['email'] = get_bloginfo( 'admin_email' );

    return wp_mail( 
      $settings['email'],
      'New Lost and Found Pets Submission',
      'There is a new submission for Lost and Found Pets',
    );
  }

  public function action_send_email( $form, $post_id ) {
    $settings['email'] = get_option( 'lostfound_settings' )['notifications_email'];

    if ( !$settings['email'] ) $settings['email'] = get_bloginfo( 'admin_email' );

    $this->send_email_notification($settings);
  }

  // Temporarily unused
  private function zerospam_get_key() {
    $key = get_option( 'lostfound_zerospam_key' );
    if ( !empty( $key ) ) return $key;

    $key = wp_generate_password( 64, false, false );
    update_option( 'lostfound_zerospam_key', $key, FALSE );
  }

  // Temporarily unused
  // Should be replace with acf/render_field hook
  public function zerospam_load_key() {
    $key = $this->zerospam_get_key();
    $sanitized_key = esc_js( $key );
    echo "<script> const lfzs_key = ${sanitized_key};</script>";
  }

} // End LostFound class

LostFound::instance();
