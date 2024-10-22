<?php
if (!function_exists('download_url')) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

/**
 * Renders the Cool Kids Signup block.
 */
function render_cool_kids_signup($attributes, $content) {
    // Start buffering output to prevent output before displaying form
    ob_start();

    // Process the signup form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ckn_signup_email'])) {
        $email = sanitize_email($_POST['ckn_signup_email']);

        // Validate email
        if (!is_email($email)) {
            return '<div class="signup-error">Invalid email address. Please enter a valid email.</div>';
        } elseif (email_exists($email)) {
            return '<div class="signup-error">Email already registered. Please use another email.</div>';
        } else {
            $user_data = fetch_random_user_data();
            if ($user_data) {
                $first_name = sanitize_text_field($user_data['first_name']);
                $last_name = sanitize_text_field($user_data['last_name']);
                $country = sanitize_text_field($user_data['country']);
                $profile_picture_url = esc_url($user_data['profile_picture']);

                // Create the user
                $user_id = wp_create_user($email, wp_generate_password(), $email);
                if (!is_wp_error($user_id)) {
                    // Update user data
                    wp_update_user([
                        'ID' => $user_id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                    ]);

                    update_user_meta($user_id, 'country', $country);

                    // Save profile picture
                    if ($profile_picture_url) {
                        $attachment_id = save_user_profile_picture($profile_picture_url, $user_id);
                        if ($attachment_id) {
                            update_user_meta($user_id, 'profile_picture_id', $attachment_id);
                        }
                    }

                    // Log the user in
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);

                    // Remove any redirection logic here, so you can handle it in cool-kids-network.php
                } else {
                    return '<div class="signup-error">An error occurred. Please try again.</div>';
                }
            } else {
                return '<div class="signup-error">Failed to fetch user data. Please try again later.</div>';
            }
        }
    }

    // Display the signup form
    return ob_get_clean() . '
    <div class="cool-kids-signup-form">
        <form method="post">
            <input type="email" name="ckn_signup_email" placeholder="Enter your email address" required>
            <button type="submit">Confirm</button>
        </form>
    </div>';
}

/**
 * Fetches user data from randomuser.me API.
 */
function fetch_random_user_data() {
    $response = wp_remote_get('https://randomuser.me/api/');
    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['results'][0])) {
        $user_info = $data['results'][0];
        return [
            'first_name' => $user_info['name']['first'],
            'last_name' => $user_info['name']['last'],
            'country' => $user_info['location']['country'],
            'profile_picture' => $user_info['picture']['large'], // Profile picture URL
        ];
    }

    return false;
}

/**
 * Saves the user profile picture from the provided URL.
 *
 * @param string $url The URL of the profile picture.
 * @param int $user_id The ID of the user.
 * @return int|false The attachment ID on success, false on failure.
 */
function save_user_profile_picture($url, $user_id) {
    // Download the image
    $temp_file = download_url($url);
    if (is_wp_error($temp_file)) {
        return false;
    }

    // Set up file information
    $file = [
        'name'     => basename($url),
        'type'     => mime_content_type($temp_file),
        'tmp_name' => $temp_file,
        'size'     => filesize($temp_file),
    ];

    // Handle the upload using WordPress functions
    $overrides = [
        'test_form' => false,
        'test_size' => true,
    ];

    $file_info = wp_handle_sideload($file, $overrides);

    // Check for upload errors
    if (!empty($file_info['error'])) {
        return false;
    }

    // Prepare attachment data
    $attachment = [
        'guid'           => $file_info['url'],
        'post_mime_type' => $file_info['type'],
        'post_title'     => sanitize_file_name(basename($file_info['file'])),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    // Insert the attachment into the media library
    $attachment_id = wp_insert_attachment($attachment, $file_info['file']);

    // Generate metadata and update attachment
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attachment_id, $file_info['file']);
    wp_update_attachment_metadata($attachment_id, $attach_data);

    // Associate the profile picture with the user
    return $attachment_id;
}
