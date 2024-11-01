<?php
/**
 * Cool Kids Demo Users Generator class
 */
class CoolKidsDemoUsersGenerator {

    public function __construct() {
        add_action('admin_post_generate_demo_users', [$this, 'generate_demo_users']);
    }

    /**
     * Display the form for generating demo users.
     */
    public function display_form() {
        ?>
        <h2><?php esc_html_e('Demo Users Generator', 'cool-kids-network'); ?></h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="generate_demo_users">
            <?php wp_nonce_field('generate_demo_users_nonce'); ?>

            <label for="number_of_users"><?php esc_html_e('Number of demo users to generate:', 'cool-kids-network'); ?></label>
            <input type="number" name="number_of_users" id="number_of_users" value="1" min="1" required>

            <p><button type="submit" class="button button-primary"><?php esc_html_e('Create', 'cool-kids-network'); ?></button></p>
        </form>
        <?php

        if (isset($_POST['generate_users'])) {
            $this->generate_demo_users(intval($_POST['number_of_users']));
        }
    }

    /**
     * Handle the form submission to generate demo users from the API.
     */
    public function generate_demo_users($number_of_users) {
        // Check for nonce validation
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'generate_demo_users_nonce')) {
            wp_die('Security check failed.');
        }
        // Set a transient to indicate success
        set_transient('cool_kids_demo_users_success', true, 5); // Expires after 5 seconds

        // Get the number of users to generate from the form input
        $num_users = intval($_POST['number_of_users']);

        if ($num_users < 1) {
            wp_die('Invalid number of users.');
        }

        // Call the randomuser.me API to generate the users
        $api_url = 'https://randomuser.me/api/?results=' . $num_users;
        $response = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            wp_die('Failed to retrieve data from the API.');
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (empty($data->results)) {
            wp_die('No users found in the API response.');
        }

        foreach ($data->results as $user_data) {
            $email = sanitize_email($user_data->email);
            $first_name = sanitize_text_field($user_data->name->first);
            $last_name = sanitize_text_field($user_data->name->last);
            $country = sanitize_text_field($user_data->location->country);
            $profile_picture_url = esc_url_raw($user_data->picture->large); // Get the profile picture URL

            // Check if the user already exists
            if (!email_exists($email)) {
                // Create a new WordPress user
                $user_id = wp_insert_user([
                    'user_login' => $email,
                    'user_email' => $email,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'role' => 'cool_kid',
                    'user_pass' => wp_generate_password(), // Generate a random password
                ]);

                // Add user meta for the country
                if (!is_wp_error($user_id)) {
                    update_user_meta($user_id, 'country', $country);

                    // Download and attach the profile picture to the user
                    $attachment_id = $this->download_profile_picture($profile_picture_url, $user_id);
                    if ($attachment_id) {
                        // Save the attachment ID in user meta as profile picture ID
                        update_user_meta($user_id, 'profile_picture_id', $attachment_id);
                    }
                }
            }
        }

        // Redirect back to the settings page with a success message
        wp_redirect(add_query_arg('demo_users_generated', $num_users, admin_url('admin.php?page=cool-kids-network-settings')));
        exit;
    }

    /**
     * Download the profile picture from the API and add it to the WordPress media library.
     *
     * @param string $profile_picture_url The URL of the profile picture.
     * @param int $user_id The ID of the user the profile picture belongs to.
     * @return int|false Attachment ID on success, false on failure.
     */
    public function download_profile_picture($profile_picture_url, $user_id) {
        // Get the file contents from the profile picture URL
        $response = wp_remote_get($profile_picture_url);
        if (is_wp_error($response)) {
            return false;
        }

        $image_data = wp_remote_retrieve_body($response);
        if (empty($image_data)) {
            return false;
        }

        // Prepare the filename and upload directory
        $filename = 'profile-picture-' . $user_id . '.jpg';
        $upload = wp_upload_bits($filename, null, $image_data);

        if ($upload['error']) {
            return false;
        }

        // Get the file path and URL
        $file_path = $upload['file'];
        $file_url = $upload['url'];

        // Prepare the attachment metadata
        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = [
            'guid'           => $file_url,
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];

        // Insert the attachment into the WordPress media library
        $attachment_id = wp_insert_attachment($attachment, $file_path);

        // Generate attachment metadata and update the attachment
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        return $attachment_id; // Return the attachment ID
    }
}
