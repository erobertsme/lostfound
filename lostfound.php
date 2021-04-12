<?php
/**
 * Plugin Name: Lost and Found
 */

if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('LostFound') ) :

register_deactivation_hook( __FILE__, array( 'LostFound', 'deactivate' ) );


Class LostFound {

  public function __construct() {
    add_action( 'init', [$this, 'initialize'] );
    add_action( 'admin_post_submit_lostfound_form', [$this, 'handle_form_submit'] );
    add_action( 'admin_post_nopriv_submit_lostfound_form', [$this, 'handle_form_submit'] );
    add_action( 'wp_ajax_submit_lostfound_form', [$this, 'handle_ajax_form_submit'] );
    add_action( 'wp_ajax_nopriv_submit_lostfound_form', [$this, 'handle_ajax_form_submit'] );
    add_action( 'wp_head', [$this, 'zerospam_load_key']);
    add_shortcode( 'lostfoundform', [$this, 'register_form_shorcode'] );
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
    delete_option( 'lostfound_zerospam_key' );
  }

  // Register Post type and Taxonomy
  private function register_cpt_tax() {
    register_post_type( 'lostfound', [
      'labels' => [
        'name' => __( 'Lost and Found Pets', 'lostfound' ),
        'singular_name' => __( 'Lost and Found Pet', 'lostfound' )
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
        'comments'
      ],
      'taxonomies' => ['category', 'pet-types', 'post_tag']
    ]);

    register_taxonomy( 'pet-types', 'lostfound', [
      'labels' => [
        'name' => __( 'Pet types', 'lostfound' ),
        'singular_name' => __( 'Pet type', 'lostfound' )
      ],
      'rewrite' => ['slug' => 'pet-types'],
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
    
    wp_insert_term('Cat', 'pet-types');
    wp_insert_term('Dog', 'pet-types');
    wp_insert_term('Other', 'pet-types');
    update_option('lostfound_terms_created', true);
  }

  private function include_acf() {
    // Define path and URL to the ACF plugin.
    define( 'LOSTFOUND_ACF_PATH', plugin_dir_path(__FILE__) . 'includes/acf/' );
    define( 'LOSTFOUND_ACF_URL', plugin_dir_url(__FILE__) . 'includes/acf/' );

    // Include the ACF plugin.
    include_once( LOSTFOUND_ACF_PATH . 'acf.php' );

    // Customize the url setting to fix incorrect asset URLs.
    add_filter('acf/settings/url',
      function( $url ) {
        return LOSTFOUND_ACF_URL;
      }
    );

    // Hide the ACF admin menu item.
    add_filter('acf/settings/show_admin', '__return_false');

    // Add ACF fields
    include_once('acf_fields.php');
  }

  private function create_post($data, $redirect_url) {
    $args = [
      'post_author' => 1,
      'post_title' => $data['title'],
      'post_status' => 'publish',
      'post_type' => 'lostfound'
    ];
    $post_id = wp_insert_post($args, true);

    if ( $post_id === 0 || is_object($post_id) ) return wp_redirect( add_query_arg( [ 'error' => $post_id->get_error_message() ], $redirect_url ) );

    wp_set_post_terms($post_id, $data['pet-type']);

    foreach ( $data as $key => $value) {
      if ($value !== '') update_field($key, $value, $post_id);
    }
  }

  function register_form_shorcode($atts) {
    wp_enqueue_script( 'lostfound_zerospam', plugin_dir_url(__FILE__) . 'includes/js/zerospam.js', [], NULL, true );
    add_action( 'wp_enqueue_scripts', 'lostfound_zerospam' );
    ob_start();
    include( 'form_template.php' );
    $form = ob_get_contents();
    ob_end_clean();
    
    return $form;

  }

  private function form_validation($form_data) {
    if ( ! isset( $form_data['lostfound_zerospam_key'] ) || $form_data['lostfound_zerospam_key'] != $this->zerospam_get_key() ) {
      $validation['pass'] = false;
      $validation['error'] = new WP_Error( 'spam_error', __("Error: Spam error", 'lostfound') );
      return $validation;
    }

    if ( empty($form_data['nonce_lostfound_form']) || !wp_verify_nonce($form_data['nonce_lostfound_form'], 'handle_lostfound_form') ) {
      $validation['pass'] = false;
      $validation['error'] = new WP_Error( 'nonce_error', __("Error: Nonce error", 'lostfound') );
      return $validation;
    }

    $error = null;

    $required_fields = [
      'lost-found',
      'location',
      'date',
      'pet-type',
      'title',
      'description',
      'name',
      'phone',
    ];

    foreach ( $required_fields as $field ) {
      if ( empty($form_data[$field]) ) {
        $validation['pass'] = false;
        $validation['error'] = new WP_Error( 'empty_error', __("Error: Missing ${field}", 'lostfound') );

        return $validation;
      }
    }

    $validation['pass'] = true;

    return $validation;
  }

  public function handle_form_submit() {
    $form_data = $_POST;
    $validation = $this->form_validation($form_data);
    $url_parts = parse_url( $form_data['_wp_http_referer'] );
    $redirect_url = $url_parts['path'];
    
    if( isset($validation['error']) ) return wp_redirect( add_query_arg( [ 'form-error' => $validation['error']->get_error_message() ], $redirect_url ) );

    // Sanitize data
    $data = [
      'lost-found'  => sanitize_text_field( $form_data['lost-found'] ),
      'location'    => sanitize_text_field( $form_data['location'] ),
      'date'        => sanitize_text_field( $form_data['date'] ),
      'pet-type'    => sanitize_key( $form_data['pet-type'] ),
      'name'        => sanitize_text_field( $form_data['name'] ),
      'phone'       => sanitize_text_field( $form_data['phone'] ),
      'email'       => sanitize_email( $form_data['email'] ),
      'title'       => sanitize_title( $form_data['title'] ),
      'description' => sanitize_textarea_field( $form_data['description'] ),
      'photo'       => sanitize_file_name( $form_data['photo'] ),
    ];

    $this->create_post($data, $redirect_url);

    return wp_redirect( add_query_arg( [ 'form-success' => '' ], $redirect_url ) );
  }

  public function handle_ajax_form_submit() {
    wp_send_json_success(['success', $_REQUEST]);
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