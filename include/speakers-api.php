<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Class JECO_SPEAKERS_API
 * 
 * Handles the API operations for the speakers table.
 */
class JECO_SPEAKERS_API
{
  private $table_name;

  public function __construct()
  {
    // Make sure the is_plugin_active() function is available.
    if (!function_exists('is_plugin_active')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    global $wpdb;
    $this->table_name = $wpdb->prefix . JECO_SPEAKERS_TABLE_NAME;

    /**
     * If the JWT Authentication for WP-API is installed, then do the full setup for a JWT Authentication method. Else, add the routes without the auth method
     */
    if (is_plugin_active('jwt-authentication-for-wp-rest-api/jwt-auth.php')) {
      // Register API routes
      add_action('rest_api_init', [$this, 'register_setup_JWT']);
      // Register API routes
      add_action('rest_api_init', [$this, 'register_routes_JWT']);
    } else {
      add_action('rest_api_init', [$this, 'register_routes']);
    }
  }

  /**
   * Register JWT auth settup if the JWT plugin is installed.
   */
  public function register_setup_JWT()
  {
    define('JWT_AUTH_SECRET_KEY', 'iP5£yI8]a>\94vY`~d£AlqCeX');
    define('JWT_AUTH_CORS_ENABLE', true);
  }

  /**
   * Registers the API routes with Auth JWT.
   */
  public function register_routes_JWT()
  {
    register_rest_route('jeco/v1', '/speakers', [
      'methods' => 'GET',
      'callback' => [$this, 'get_speakers'],
      'permission_callback' => [$this, 'check_permissions']
    ]);

    register_rest_route('jeco/v1', '/speakers/(?P<id>\d+)', [
      'methods' => 'GET',
      'callback' => [$this, 'get_speaker'],
      'permission_callback' => [$this, 'check_permissions']
    ]);

    register_rest_route('jeco/v1', '/speakers', [
      'methods' => 'POST',
      'callback' => [$this, 'create_speaker'],
      'permission_callback' => [$this, 'check_permissions']
    ]);

    register_rest_route('jeco/v1', '/speakers/(?P<id>\d+)', [
      'methods' => 'PUT',
      'callback' => [$this, 'update_speaker'],
      'permission_callback' => [$this, 'check_permissions']
    ]);

    register_rest_route('jeco/v1', '/speakers/(?P<id>\d+)', [
      'methods' => 'DELETE',
      'callback' => [$this, 'delete_speaker'],
      'permission_callback' => [$this, 'check_permissions']
    ]);
  }

  /**
   * Registers the API routes with Auth JWT.
   */
  public function register_routes()
  {
    register_rest_route('jeco/v1', '/speakers', [
      'methods' => 'GET',
      'callback' => [$this, 'get_speakers']
    ]);

    register_rest_route('jeco/v1', '/speakers/(?P<id>\d+)', [
      'methods' => 'GET',
      'callback' => [$this, 'get_speaker']
    ]);

    register_rest_route('jeco/v1', '/speakers', [
      'methods' => 'POST',
      'callback' => [$this, 'create_speaker']
    ]);

    register_rest_route('jeco/v1', '/speakers/(?P<id>\d+)', [
      'methods' => 'PUT',
      'callback' => [$this, 'update_speaker']
    ]);

    register_rest_route('jeco/v1', '/speakers/(?P<id>\d+)', [
      'methods' => 'DELETE',
      'callback' => [$this, 'delete_speaker']
    ]);
  }

  /**
   * Check permissions (modify as needed for authentication).
   */
  public function check_permissions()
  {
    return current_user_can('manage_options'); // Example: Restrict to admins
  }

  /**
   * Retrieves a list of speakers.
   */
  public function get_speakers($request)
  {
    global $wpdb;
    $speakers = $wpdb->get_results("SELECT * FROM {$this->table_name}");
    if ($speakers) {
      return rest_ensure_response($speakers);
    }
    return new WP_Error('no_speakers', 'No speakers found', ['status' => 404]);
  }

  /**
   * Retrieves a single speaker by ID.
   */
  public function get_speaker($request)
  {
    global $wpdb;
    $speaker_id = $request['id'];
    $speaker = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE id = %d", $speaker_id));
    if ($speaker) {
      return rest_ensure_response($speaker);
    }
    return new WP_Error('no_speaker', 'Speaker not found', ['status' => 404]);
  }

  /**
   * Creates a new speaker.
   */
  public function create_speaker($request)
  {
    global $wpdb;

    // Data from the request
    $name = sanitize_text_field($request['name']);
    $last_name = sanitize_text_field($request['last_name']);
    $email = sanitize_email($request['email']);
    $phone = sanitize_text_field($request['phone']);
    $location = sanitize_text_field($request['location']);

    $result = $wpdb->insert(
      $this->table_name,
      [
        'name' => $name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'location' => $location
      ],
      ['%s', '%s', '%s', '%s', '%s']
    );

    if ($result) {
      return rest_ensure_response(['message' => 'Speaker added successfully']);
    }
    return new WP_Error('create_failed', 'Failed to create speaker', ['status' => 500]);
  }

  /**
   * Updates an existing speaker.
   */
  public function update_speaker($request)
  {
    global $wpdb;

    $speaker_id = $request['id'];

    // Data from the request
    $name = sanitize_text_field($request['name']);
    $last_name = sanitize_text_field($request['last_name']);
    $email = sanitize_email($request['email']);
    $phone = sanitize_text_field($request['phone']);
    $location = sanitize_text_field($request['location']);

    $result = $wpdb->update(
      $this->table_name,
      [
        'name' => $name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'location' => $location
      ],
      ['id' => $speaker_id],
      ['%s', '%s', '%s', '%s', '%s'],
      ['%d']
    );

    if ($result !== false) {
      return rest_ensure_response(['message' => 'Speaker updated successfully']);
    }
    return new WP_Error('update_failed', 'Failed to update speaker', ['status' => 500]);
  }

  /**
   * Deletes a speaker.
   */
  public function delete_speaker($request)
  {
    global $wpdb;
    $speaker_id = $request['id'];

    $result = $wpdb->delete($this->table_name, ['id' => $speaker_id], ['%d']);

    if ($result) {
      return rest_ensure_response(['message' => 'Speaker deleted successfully']);
    }
    return new WP_Error('delete_failed', 'Failed to delete speaker', ['status' => 500]);
  }
}

// Initialize the API
new JECO_SPEAKERS_API();
