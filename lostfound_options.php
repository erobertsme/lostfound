<?php
add_action( 'admin_menu', 'lostfound_add_admin_menu' );
add_action( 'admin_init', 'lostfound_settings_init' );


function lostfound_add_admin_menu() { 

  $parent_slug = 'edit.php?post_type=lostfound';
    $page_title = 'Lost and Found Pets - Settings';
    $menu_title = 'Settings';
    $capability = 'edit_pages';
    $menu_slug = 'lostfound-settings';
    $function = 'lostfound_options_page';

  add_submenu_page(
    $parent_slug,
    $page_title,
    $menu_title,
    $capability,
    $menu_slug,
    $function
  );

}


function lostfound_settings_init() { 

  register_setting( 'lostfound_settings_group', 'lostfound_settings' );

  add_settings_section(
    'lostfound_settings_group_section', 
    __( 'Settings', 'lostfound' ), 
    'lostfound_settings_section_callback', 
    'lostfound_settings_group'
  );

  add_settings_field( 
    'notifications_email', 
    __( 'Notification Email', 'lostfound' ), 
    'notifications_email_render', 
    'lostfound_settings_group', 
    'lostfound_settings_group_section' 
  );

  add_settings_field( 
    'new_post_status', 
    __( 'New Post Status', 'lostfound' ), 
    'new_post_status_render', 
    'lostfound_settings_group', 
    'lostfound_settings_group_section' 
  );

}


function notifications_email_render() { 

  $option = get_option( 'lostfound_settings' )['notifications_email'];
  ?>
<input type="email" name="lostfound_settings[notifications_email]" value="<?php echo $option; ?>" required>
  <?php

}


function new_post_status_render() { 

  $option = get_option( 'lostfound_settings' )['new_post_status'];
  ?>
<select name='lostfound_settings[new_post_status]' required>
  <option value="publish" <?php selected( $option, 1 ); ?>>Published</option>
  <option value="pending" <?php selected( $option, 2 ); ?>>Pending</option>
</select>
  <?php

}


function lostfound_settings_section_callback() { 
  ?>
<p>Set the Notification Email setting to the email address that will receive new submission notifications. Default: admin email</p>
<p>The new post status determines whether new posts are published automatically or wait for admin approval. Default: Pending</p>
  <?php
}


function lostfound_options_page() { 

  ?>
<div class="container">
<h1>Lost and Found</h1>
  <form action="options.php" method="post">
    <div class="settings">
      <?php
      settings_fields( 'lostfound_settings_group' );
      do_settings_sections( 'lostfound_settings_group' );
      ?>
    </div>
    <div class="save">
      <?php submit_button(); ?>
    </div>

  </form>
</div>
  <?php

}
