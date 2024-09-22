<?php
// Edit Speaker Form
if (isset($_GET['id'])) {
  $speaker_id = (int)$_GET['id'];

  //submitting data
  if (isset($_POST['submit_edit_speaker']) && check_admin_referer('edit_speaker', 'edit_speaker_nonce')) {
    $speaker_data = (object)[
      'id' => $speaker_id,
      'name' => sanitize_text_field($_POST['name']),
      'last_name' => sanitize_text_field($_POST['last_name']),
      'email' => sanitize_email($_POST['email']),
      'phone' => sanitize_text_field($_POST['phone']),
      'location' => sanitize_text_field($_POST['location']),
    ];

    if ($this->crud->update_speaker($speaker_id, $speaker_data)) {
      echo '<div class="notice notice-success is-dismissible"><p>Speaker updated successfully!</p></div>';
    } else {
      echo '<div class="notice notice-error is-dismissible"><p>Failed to update speaker. Please try again.</p></div>';
    }
  }//submitting data
  
  $speaker = $this->crud->get_speaker_by_id($speaker_id);
?>
  <div class="wrap">
    <h1>Edit Speaker</h1>
    <form method="post">
      <?php wp_nonce_field('edit_speaker', 'edit_speaker_nonce'); ?>
      <table class="form-table">
        <tr>
          <th>Name</th>
          <td><input type="text" name="name" value="<?php echo esc_attr($speaker->name); ?>" required></td>
        </tr>
        <tr>
          <th>Last Name</th>
          <td><input type="text" name="last_name" value="<?php echo esc_attr($speaker->last_name); ?>" required></td>
        </tr>
        <tr>
          <th>Email</th>
          <td><input type="email" name="email" value="<?php echo esc_attr($speaker->email); ?>" required></td>
        </tr>
        <tr>
          <th>Phone</th>
          <td><input type="text" name="phone" value="<?php echo esc_attr($speaker->phone); ?>"></td>
        </tr>
        <tr>
          <th>Location</th>
          <td><input type="text" name="location" value="<?php echo esc_attr($speaker->location); ?>"></td>
        </tr>
      </table>
      <div class="submit">
        <input type="submit" name="submit_edit_speaker" class="button-primary" value="Update Speaker">
      </div>
    </form>
  </div>
<?php
}
