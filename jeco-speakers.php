<?php
/*
Plugin Name: Jeco Speakers
Description: Add a full CRUD for a Speakers table
Version: 1.0
Author: Jesus Carrero
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('JECO_SPEAKERS')) {

	/**
	 * Class JECO_SPEAKERS
	 *
	 * This class handles the functionality of the JECO Speakers plugin.
	 */
	class JECO_SPEAKERS
	{
		/**
		 * This variable will handle the crud, as an instance of the class JECO_SPEAKERS_CRUD.
		 * @var 
		 */
		public $crud;

		/**
		 * Plugin version number.
		 *
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * JECO_SPEAKERS constructor.
		 *
		 * Initialize hooks and constants.
		 */
		public function __construct()
		{
			// Define constants for the plugin
			$this->define('JECO_SPEAKERS_PLUGIN_NAME', 'JECO_SPEAKERS');
			$this->define('JECO_SPEAKERS_TABLE_NAME', 'jeco_speakers');
			$this->define('JECO_SPEAKERS_VERSION', $this->version);
			$this->define('JECO_SPEAKERS_ROOT_URL', plugin_dir_url(__FILE__));
			$this->define('JECO_SPEAKERS_ROOT_PATH', plugin_dir_path(__FILE__));

			/**
			 * Include necessary files
			 * such as the JECO_SPEAKERS_CRUD class.
			 */
			$this->include_files();

			// Initialize CRUD class
			$this->crud = new JECO_SPEAKERS_CRUD();

			// Initialize hooks
			$this->init_hooks();
		}

		/**
		 * Include necessary files for the plugin.
		 *
		 * @return void
		 */
		public function include_files(): void
		{
			include_once plugin_dir_path(__FILE__) . 'include/speakers-crud.php';
		}


		/**
		 * Initialize hooks for the plugin.
		 *
		 * This method sets up activation, deactivation hooks, cron jobs, and admin menu.
		 *
		 * @return void
		 */
		public function init_hooks(): void
		{
			register_activation_hook(__FILE__, [$this, 'activation_function']);
			register_deactivation_hook(__FILE__, [$this, 'deactivation_function']);

			// Admin menu hook
			add_action('admin_menu', [$this, 'jeco_speakers_admin_menu']);
		}

		public function jeco_speakers_admin_menu(): void
		{
			add_menu_page(
				'Speakers Manager',  // Page title
				'Jeco Speakers',          // Menu title
				'manage_options',    // Capability
				'jeco-speakers',     // Menu slug
				[$this, 'jeco_speakers_page'],  // Function
				'dashicons-megaphone',  // Icon
				100                   // Position
			);
		}

		/**
		 * Summary of jeco_speakers_page
		 * Main page of the plugin. It has the button to create new jeco-speakers.
		 * It shows all the speakers that are included.
		 * It has the edit and delete options.
		 * @return void
		 */
		public function jeco_speakers_page(): void
		{
			// Delete
			if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
				if (check_admin_referer('delete_speaker_' . (int)$_GET['id'])) {
					$response = $this->crud->delete_speaker((int)$_GET['id']);
					if ($response) {
						echo '<div class="notice notice-success is-dismissible"><p>Speaker deleted successfully!</p></div>';
					} else
						echo '<div class="notice notice-error is-dismissible"><p>Failed to delete speaker. Please try again.</p></div>';
				}
			}

			// Edit
			if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
				$speaker_id = (int)$_GET['id'];

				// Handle form submission for updating
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
				}

				$speaker = $this->crud->get_speaker_by_id($speaker_id);
				// Render form for editing
				echo '<div class="wrap">';
				echo '<h1>Edit Speaker</h1>';
				echo '<form method="post">';
				wp_nonce_field('edit_speaker', 'edit_speaker_nonce');
				echo '<table class="form-table">';
				echo '<tr><th>Name</th><td><input type="text" name="name" value="' . esc_attr($speaker->name) . '" required></td></tr>';
				echo '<tr><th>Last Name</th><td><input type="text" name="last_name" value="' . esc_attr($speaker->last_name) . '" required></td></tr>';
				echo '<tr><th>Email</th><td><input type="email" name="email" value="' . esc_attr($speaker->email) . '" required></td></tr>';
				echo '<tr><th>Phone</th><td><input type="text" name="phone" value="' . esc_attr($speaker->phone) . '"></td></tr>';
				echo '<tr><th>Location</th><td><input type="text" name="location" value="' . esc_attr($speaker->location) . '"></td></tr>';
				echo '</table>';
				echo '<p class="submit"><input type="submit" name="submit_edit_speaker" class="button-primary" value="Update Speaker"></p>';
				echo '</form>';
				echo '</div>';

				return;
			}

			// Create
			if (isset($_GET['action']) && $_GET['action'] === 'add') {
				if (isset($_POST['submit_add_speaker']) && check_admin_referer('add_speaker', 'add_speaker_nonce')) {
					// Handle form submission to add a new speaker
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

				// Render the form for adding a new speaker
				echo '<div class="wrap">';
				echo '<h1>Add New Speaker</h1>';
				echo '<form method="post">';
				wp_nonce_field('add_speaker', 'add_speaker_nonce');
				echo '<table class="form-table">';
				echo '<tr><th>Name</th><td><input type="text" name="name" required></td></tr>';
				echo '<tr><th>Last Name</th><td><input type="text" name="last_name" required></td></tr>';
				echo '<tr><th>Email</th><td><input type="email" name="email" required></td></tr>';
				echo '<tr><th>Phone</th><td><input type="text" name="phone"></td></tr>';
				echo '<tr><th>Location</th><td><input type="text" name="location"></td></tr>';
				echo '</table>';
				echo '<p class="submit"><input type="submit" name="submit_add_speaker" class="button-primary" value="Add Speaker"></p>';
				echo '</form>';
				echo '</div>';

				return;
			}

			// Display the speakers list if no add/edit action is taken
			$speakers = $this->crud->get_all_speakers();

			echo '<div class="wrap">';
			echo '<h1>Speakers List</h1>';

			echo '<a href="?page=jeco-speakers&action=add" class="button-primary">Add New Speaker</a>';

			// Display speakers in a table
			echo '<table class="wp-list-table widefat fixed striped">';
			echo '<thead><tr><th>Name</th><th>Last Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>';
			echo '<tbody>';

			foreach ($speakers as $speaker) {
				echo '<tr>';
				echo '<td>' . esc_html($speaker->name) . '</td>';
				echo '<td>' . esc_html($speaker->last_name) . '</td>';
				echo '<td>' . esc_html($speaker->email) . '</td>';
				echo '<td>' . esc_html($speaker->phone) . '</td>';
				echo '<td>';
				echo '<a href="' . esc_url(wp_nonce_url('?page=jeco-speakers&action=edit&id=' . $speaker->id, 'edit_speaker_' . $speaker->id)) . '">Edit</a> | ';
				echo '<a href="' . esc_url(wp_nonce_url('?page=jeco-speakers&action=delete&id=' . $speaker->id, 'delete_speaker_' . $speaker->id)) . '" onclick="return confirm(\'Are you sure you want to delete this speaker?\')">Delete</a>';
				echo '</td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '</table>';
			echo '</div>';
		} // jeco_speakers_page

		/**
		 * Activation function.
		 *
		 * This function is triggered when the plugin is activated.
		 *
		 * @return void
		 */
		public function activation_function(): void
		{
			// Creating the table
			$this->crud->create_speakers_table();

			// Call the function to fetch users and insert into the database
			$this->fetch_and_insert_speakers();

			add_action('rest_api_init', function () {
				register_rest_route('jeco-speakers', "speakers", array(
					'methods' => 'GET',
					'callback' => [$this, 'get_speakers']

				));
			});
		}

		/**
		 * Fetch users from randomuser.me and insert into the speakers table.
		 *
		 * @return void
		 */
		public function fetch_and_insert_speakers(): void
		{
			global $wpdb;
			$table_name = $wpdb->prefix . JECO_SPEAKERS_TABLE_NAME;

			// Call the randomuser.me API
			$response = wp_remote_get('https://randomuser.me/api/?results=50');

			// Check for any errors
			if (is_wp_error($response)) {
				error_log('Failed to fetch users from randomuser.me: ' . $response->get_error_message());
				return;
			}

			// Get the response body
			$body = wp_remote_retrieve_body($response);

			// Decode the JSON response
			$data = json_decode($body);

			if (!isset($data->results)) {
				error_log('Unexpected API response format');
				return;
			}

			// Prepare an array to hold the values for bulk insert
			$values = [];
			$placeholders = [];

			// Loop through each user and prepare the data for bulk insert
			foreach ($data->results as $user) {
				$name = sanitize_text_field($user->name->first);
				$last_name = sanitize_text_field($user->name->last);
				$location = sanitize_textarea_field($user->location->city . ', ' . $user->location->country); // Example of location
				$email = sanitize_email($user->email);
				$phone = sanitize_text_field($user->phone);
				//$picture_url = esc_url_raw($user->picture->large);

				// Add the values to the array
				$values[] = $name;
				$values[] = $last_name;
				$values[] = $location;
				$values[] = $email;
				$values[] = $phone;

				// Prepare placeholders for the query
				$placeholders[] = "(%s, %s, %s, %s, %s)";
			}

			// Construct the SQL query for bulk insert
			$sql = "INSERT INTO $table_name (name, last_name, location, email, phone) VALUES " . implode(', ', $placeholders);

			// Execute the bulk insert query
			$wpdb->query($wpdb->prepare($sql, ...$values));
		}


		public function get_speakers(): WP_REST_Response
		{
			global $wpdb;
			$table_name = $wpdb->prefix . JECO_SPEAKERS_TABLE_NAME;
			$results = $wpdb->get_results("SELECT * FROM $table_name");

			return new WP_REST_Response($results, 200);
		}

		/**
		 * Deactivation function.
		 *
		 * @return void
		 */
		public function deactivation_function(): void
		{
			$this->crud->drop_speakers_table();
		}

		/**
		 * Defines a constant if it is not already defined.
		 *
		 * @param string $name  The constant name.
		 * @param mixed  $value The constant value.
		 *
		 * @return void
		 */
		public function define($name, $value = true): void
		{
			if (!defined($name)) {
				define($name, $value);
			}
		}
	}

	/**
	 * Returns the one true instance of the JECO_SPEAKERS class.
	 *
	 * This function ensures that the class is instantiated only once.
	 *
	 * @return JECO_SPEAKERS
	 */
	function jeco_speakers(): JECO_SPEAKERS
	{
		global $jeco_speakers;

		if (!isset($jeco_speakers)) {
			$jeco_speakers = new JECO_SPEAKERS();
		}

		return $jeco_speakers;
	}

	// Instantiate the plugin
	jeco_speakers();
}
