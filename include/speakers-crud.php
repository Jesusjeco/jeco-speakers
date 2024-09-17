<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Class JECO_SPEAKERS_CRUD
 * 
 * Handles the CRUD operations for the speakers table.
 */
class JECO_SPEAKERS_CRUD
{
  private $table_name;

  public function __construct()
  {
    global $wpdb;
    $this->table_name = $wpdb->prefix . JECO_SPEAKERS_TABLE_NAME;
  }

  public function create_speakers_table(): void
  {
    global $wpdb;

    // Check if the table already exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$this->table_name'");

    if ($table_exists === $this->table_name) {
      // Table already exists, no need to create it
      return;
    }

    // SQL query to create the table if it doesn't exist
    $charset_collate = $wpdb->get_charset_collate();
    $sql = <<<TEXT
        CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            last_name varchar(255),
            email varchar(255),
            phone varchar(20),
            location text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;
    TEXT;

    // Include the upgrade.php file to use dbDelta function
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  public function drop_speakers_table(): void
  {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS $this->table_name");
  }

  /**
   * Create a new speaker entry.
   *
   * @param array $data
   * @return bool
   */
  public function create_speaker(object $speaker_data): bool
  {
    global $wpdb;

    return $wpdb->insert(
      $this->table_name,
      [
        'name' => $speaker_data->name,
        'last_name' => $speaker_data->last_name,
        'email' => $speaker_data->email,
        'phone' => $speaker_data->phone,
        'location' => $speaker_data->location,
      ]
    );
  }

  /**
   * Update an existing speaker entry.
   *
   * @param int $id
   * @param array $data
   * @return bool|int
   */
  public function update_speaker(int $id, object $speaker_data)
  {
    global $wpdb;

    return $wpdb->update(
      $this->table_name,
      [
        'name' => $speaker_data->name,
        'last_name' => $speaker_data->last_name,
        'email' => $speaker_data->email,
        'phone' => $speaker_data->phone,
        'location' => $speaker_data->location,
      ],
      ['id' => $id]
    );
  }

  /**
   * Delete a speaker entry.
   *
   * @param int $id
   * @return bool|int
   */
  public function delete_speaker(int $id): bool|int
  {
    global $wpdb;

    $response =  $wpdb->delete(
      $this->table_name,
      ['id' => $id]
    );

    return $response;
  }

  /**
   * Get a single speaker by ID.
   *
   * @param int $id
   * @return object|null
   */
  public function get_speaker_by_id(int $id)
  {
    global $wpdb;

    return $wpdb->get_row(
      $wpdb->prepare(
        "SELECT * FROM $this->table_name WHERE id = %d",
        $id
      )
    );
  }

  /**
   * Get all speakers.
   *
   * @return array|object|null
   */
  public function get_all_speakers()
  {
    global $wpdb;

    return $wpdb->get_results(
      "SELECT * FROM $this->table_name ORDER BY id DESC"
    );
  }
}// JECO_SPEAKERS_CRUD