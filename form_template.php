<?php if( false || isset($_GET['form-success']) ): ?>
<div class="form-success">Thank you for your submission!</div>
<?php return; endif; ?>
<form id="lostfound" method="POST" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
  <?php if( isset($_GET['form-error']) ): ?>
  <div class="form-error"><?php echo urldecode( $_GET['form-error'] ); ?></div>
  <?php endif; ?>
  <?php if( isset($_GET['error']) ): ?>
  <div class="form-error"><?php echo urldecode( $_GET['error'] ); ?></div>
  <?php endif; ?>

  <input name="action" type="hidden" value="submit_lostfound_form" />

  <div class="field-group">
    <label for="lost-found">Lost or found?</label>
    <select name="lost-found">
      <option value="lost">Lost</option>
      <option value="found">Found</option>
    </select>
  </div>

  <div class="field-group">
    <label for="location">Location where pet was lost/found<span class="required">*</span></label>
    <input name="location" type="text" required />
  </div>

  <div class="field-group">
    <label for="date">Date pet was lost/found<span class="required">*</span></label>
    <input name="date" type="date" required />
  </div>

  <div class="field-group">
    <label for="pet-type">Type of pet</label>
    <select name="pet-type" required>
    <?php
      $terms = get_terms([
        'taxonomy' => 'pet-types',
        'fields' => 'id=>name',
        'hide_empty' => false
      ]);
      foreach ( $terms as $key => $term ): 
    ?>
      <option value="<?php echo $key; ?>"><?php echo $term; ?></option>
    <?php endforeach; ?>
    </select>
  </div>

  <div class="field-group">
    <label for="title">Title<span class="required">*</span></label>
    <input name="title" type="text" required />
  </div>

  <div class="field-group">
    <label for="description">Description<span class="required">*</span></label>
    <textarea name="description" type="textarea" cols="30" rows="5" required></textarea>
  </div>

  <div class="field-group">
    <label for="name">Your name<span class="required">*</span></label>
    <input name="name" type="text" required />
  </div>

  <div class="field-group">
    <label for="phone">Your phone number<span class="required">*</span></label>
    <input name="phone" type="tel" required />
  </div>

  <div class="field-group">
    <label for="email">Your email</label>
    <input name="email" type="email" />
  </div>

  <div class="field-group">
    <label for="photo">Photo</label>
    <input name="photo" type="file"  />
  </div>

  <?php wp_nonce_field('handle_lostfound_form', 'nonce_lostfound_form'); ?>

  <input type="submit" value="Submit" />
</form>