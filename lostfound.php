<?php
/**
 * Plugin Name: Lost and Found
 * Version: 1.1
 * Description: Creates a shortcode to display a form which allows users to submit to a Lost and Found custom post type with custom fields and a custom Taxonomy. Use <strong>[lostfound_form]</strong> to display the form.
 * Plugin URI: https://github.com/omfgtora/lostfound
 * Author: Ethan Roberts
 * License: GPL v3 or later
 * Licence URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: lostfound
 */

if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('LostFound') ) :

register_deactivation_hook( __FILE__, array( 'LostFound', 'deactivate' ) );

Class LostFound {

  public function __construct() {
    add_action( 'init', [$this, 'initialize'] );
    //add_action( 'wp_head', [$this, 'zerospam_load_key']);
    add_shortcode( 'lostfound_form', [$this, 'register_form_shorcode'] );
    include_once('acf_fields.php');
  }

  public function initialize() {
    // Bail early if called directly from functions.php or plugin file.
    if( !did_action('plugins_loaded') ) return;

    $this->register_cpt_tax();
    $this->create_default_terms();
    $this->include_acf();
  }

  public function deactivate() {
    delete_option( 'lostfound_terms_created' );
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
      'rewrite' => ['slug' => 'lostfound'],
      'supports' => [
        'title',
        'editor',
        'author',
        'thumbnail',
        'custom-fields',
        'pet-types',
        'comments',
      ],
      'taxonomies' => ['pet-types', 'post_tag'],
    ]);

    register_taxonomy( 'pet-type', 'lostfound', [
      'labels' => [
        'name' => __( 'Pet type', 'lostfound' ),
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

  private function create_default_terms() {
    if ( get_option('lostfound_terms_created') ) return;

    wp_insert_term('Cat', 'pet-type');
    wp_insert_term('Dog', 'pet-type');
    wp_insert_term('Other', 'pet-type');

    update_option('lostfound_terms_created', true);
  }

  private function include_acf() {
    // Define path and URL to the ACF plugin.
    define( 'LOSTFOUND_ACF_PATH', plugin_dir_path(__FILE__) . 'includes/acf/' );
    define( 'LOSTFOUND_ACF_URL', plugin_dir_url(__FILE__) . 'includes/acf/' );

    // Include the ACF plugin.
    include_once( LOSTFOUND_ACF_PATH . 'acf.php' );

    // Customize the url setting to fix incorrect asset URLs.
    add_filter('acf/settings/url', function( $url ) {
        return LOSTFOUND_ACF_URL;
    });

    // Hide the ACF admin menu item.
    add_filter('acf/settings/show_admin', '__return_false');
  }

  function register_form_shorcode($atts) {
    // wp_enqueue_script( 'lostfound_zerospam', plugin_dir_url(__FILE__) . 'includes/js/zerospam.js', [], NULL, true );
    // add_action( 'wp_enqueue_scripts', 'lostfound_zerospam' );

    $settings = [
      'id' => 'lostfound-form',
      'post_id' => 'new_post',
      'new_post' => [
        'post_type'   => 'lostfound',
        'post_status' => 'pending'
      ],
      'field_groups' => ['lostfound-form-groups'],
      'form' => true,
      'updated_message' => __("Thank you for your submission!", 'acf'),
      'honeypot' => true,
      'submit_value' => __("Submit", 'acf'),
    ];
    
    ob_start();

    acf_form_head();
    acf_form( $settings );
    $form = ob_get_contents();
    
    ob_end_clean();
  
    return $form;
  }

  public function zerospam_get_key() {
    if ( ! $key = get_option('lostfound_zerospam_key') ) {
      $key = wp_generate_password( 64, false, false );
      update_option( 'lostfound_zerospam_key', $key, FALSE );
    }
    return $key;
  }

  public function zerospam_load_key() {
    ?>
<script> const lfzs_key = '<?php echo $this->zerospam_get_key(); ?>';</script>
    <?php
  }

} // End LostFound class

function lostfound() {
	global $lostfound;
	
	// Instantiate only once.
	if( !isset($lostfound) ) {
		$lostfound = new LostFound();
		$lostfound->initialize();
	}
	return $lostfound;
}

// Instantiate.
lostfound();

endif; // class_exists check