<?php
// Display Speakers List

// if dubmitting a Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
  if (check_admin_referer('delete_speaker_' . (int)$_GET['id'])) {
    if ($this->crud->delete_speaker((int)$_GET['id'])) {
      echo '<div class="notice notice-success is-dismissible"><p>Speaker deleted successfully!</p></div>';
    } else
      echo '<div class="notice notice-error is-dismissible"><p>Failed to delete speaker. Please try again.</p></div>';
  }
} //Delete
$speakers = $this->crud->get_all_speakers();
?>
<div class="wrap">
  <h1>Speakers List</h1>
  <a href="?page=jeco-speakers&action=add" class="button-primary">Add New Speaker</a>
  <table class="wp-list-table widefat fixed striped">
    <thead>
      <tr>
        <th>Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($speakers as $speaker): ?>
        <tr>
          <td><?php echo esc_html($speaker->name); ?></td>
          <td><?php echo esc_html($speaker->last_name); ?></td>
          <td><?php echo esc_html($speaker->email); ?></td>
          <td><?php echo esc_html($speaker->phone); ?></td>
          <td>
            <a href="<?php echo esc_url(wp_nonce_url('?page=jeco-speakers&action=edit&id=' . $speaker->id, 'edit_speaker_' . $speaker->id)); ?>">Edit</a> |
            <a href="<?php echo esc_url(wp_nonce_url('?page=jeco-speakers&action=delete&id=' . $speaker->id, 'delete_speaker_' . $speaker->id)); ?>" onclick="return confirm('Are you sure you want to delete this speaker?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>