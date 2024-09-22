<?php
if (isset($_POST['submit_add_speaker']) && check_admin_referer('add_speaker', 'add_speaker_nonce')) {
  $speaker_data = (object)[
    'name' => sanitize_text_field($_POST['name']),
    'last_name' => sanitize_text_field($_POST['last_name']),
    'email' => sanitize_email($_POST['email']),
    'phone' => sanitize_text_field($_POST['phone']),
    'location' => sanitize_text_field($_POST['location']),
  ];

  if ($this->crud->create_speaker($speaker_data)) {
    echo '<div class="notice notice-success is-dismissible"><p>Speaker added successfully!</p></div>';
  } else {
    echo '<div class="notice notice-error is-dismissible"><p>Failed to add speaker. Please try again.</p></div>';
  }
}
?>
<div class="wrap">
  <h1>Add New Speaker</h1>
  <form method="post">
    <?php wp_nonce_field('add_speaker', 'add_speaker_nonce'); ?>
    <table class="form-table">
      <tr>
        <th>Name</th>
        <td><input type="text" name="name" required></td>
      </tr>
      <tr>
        <th>Last Name</th>
        <td><input type="text" name="last_name" required></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><input type="email" name="email" required></td>
      </tr>
      <tr>
        <th>Phone</th>
        <td><input type="text" name="phone"></td>
      </tr>
      <tr>
        <th>Location</th>
        <td><input type="text" name="location"></td>
      </tr>
    </table>
    <div class="submit">
      <input type="submit" name="submit_add_speaker" class="button-primary" value="Add Speaker">
    </div>
  </form>
</div>