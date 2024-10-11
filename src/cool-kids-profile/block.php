<?php
/**
 * Renders the Cool Kids Profile block.
 */
function render_cool_kids_profile($attributes, $content) {
    // Check if the user is logged in
    if (!is_user_logged_in()) {
        return '<p>Please log in to view your profile information.</p>';
    }

    // Get current user data
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);
    $country = get_user_meta($user_id, 'country', true);
    $email = $current_user->user_email;
    $role = implode(', ', $current_user->roles);

    // Get profile picture URL
    $profile_picture_url = get_user_profile_picture_url($user_id);
    if (!$profile_picture_url) {
        $profile_picture_url = 'https://via.placeholder.com/150'; // Default placeholder image
    }

    // Output the profile information
    ob_start();
    ?>
    <div class="cool-kids-profile">
        <img src="<?php echo esc_url($profile_picture_url); ?>" alt="Profile Picture" class="profile-picture" />
        <p><strong>First Name:</strong> <?php echo esc_html($first_name); ?></p>
        <p><strong>Last Name:</strong> <?php echo esc_html($last_name); ?></p>
        <p><strong>Country:</strong> <?php echo esc_html($country); ?></p>
        <p><strong>Email:</strong> <?php echo esc_html($email); ?></p>
        <p><strong>Role:</strong> <?php echo esc_html($role); ?></p>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Fetches the user's profile picture URL.
 *
 * @param int $user_id The ID of the user.
 * @return string|false The URL of the profile picture, or false if not set.
 */
function get_user_profile_picture_url($user_id) {
    $attachment_id = get_user_meta($user_id, 'profile_picture_id', true);
    if ($attachment_id) {
        return wp_get_attachment_url($attachment_id);
    }
    return false; // Fallback if no profile picture is set
}
