<?php
/**
 * Renders the Cool Kids User Directory block with filters.
 */
function render_cool_kids_user_directory($attributes, $content) {
    // Get all unique countries from users
    $all_users = get_users(['role__in' => ['cool_kid', 'cooler_kid', 'coolest_kid']]);
    $countries = [];
    foreach ($all_users as $user) {
        $country = get_user_meta($user->ID, 'country', true);
        if ($country && !in_array($country, $countries)) {
            $countries[] = $country;
        }
    }
    sort($countries); // Sort countries alphabetically

    // Define the mapping of role slugs to role names
    $role_names = [
        'cool_kid' => 'Cool Kid',
        'cooler_kid' => 'Cooler Kid',
        'coolest_kid' => 'Coolest Kid',
    ];

    // Get selected filters from query parameters
    $selected_country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
    $selected_role = isset($_GET['role']) ? sanitize_text_field($_GET['role']) : '';

    // Query users with selected filters
    $args = [
        'role__in' => ['cool_kid', 'cooler_kid', 'coolest_kid'],
        'meta_query' => [],
    ];

    if ($selected_country) {
        $args['meta_query'][] = [
            'key' => 'country',
            'value' => $selected_country,
            'compare' => '='
        ];
    }

    if ($selected_role) {
        $args['role'] = $selected_role;
    }

    $user_query = new WP_User_Query($args);

    // Start output buffering
    ob_start();
    ?>

    <h2><?php esc_html_e('User Directory', 'cool-kids-network'); ?></h2>

    <!-- Filter Form -->
    <form method="get" class="cool-kids-user-directory-filters">
        <label for="country"><?php esc_html_e('Country:', 'cool-kids-network'); ?></label>
        <select name="country" id="country" onchange="this.form.submit()">
            <option value=""><?php esc_html_e('All Countries', 'cool-kids-network'); ?></option>
            <?php foreach ($countries as $country) : ?>
                <option value="<?php echo esc_attr($country); ?>" <?php selected($selected_country, $country); ?>>
                    <?php echo esc_html($country); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="role"><?php esc_html_e('Role:', 'cool-kids-network'); ?></label>
        <select name="role" id="role" onchange="this.form.submit()">
            <option value=""><?php esc_html_e('All Roles', 'cool-kids-network'); ?></option>
            <?php foreach ($role_names as $role_slug => $role_name) : ?>
                <option value="<?php echo esc_attr($role_slug); ?>" <?php selected($selected_role, $role_slug); ?>>
                    <?php echo esc_html($role_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- User Directory Table -->
    <table class="cool-kids-user-directory">
        <thead>
            <tr>
                <th><?php esc_html_e('Profile Picture', 'cool-kids-network'); ?></th>
                <th><?php esc_html_e('First Name', 'cool-kids-network'); ?></th>
                <th><?php esc_html_e('Last Name', 'cool-kids-network'); ?></th>
                <th><?php esc_html_e('Country', 'cool-kids-network'); ?></th>
                <th><?php esc_html_e('Email', 'cool-kids-network'); ?></th>
                <th><?php esc_html_e('Role', 'cool-kids-network'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($user_query->get_results())) : ?>
                <tr>
                    <td colspan="6"><?php esc_html_e('No users found.', 'cool-kids-network'); ?></td>
                </tr>
            <?php else : ?>
                <?php foreach ($user_query->get_results() as $user) : ?>
                    <?php
                    // Get user profile information
                    $first_name = get_user_meta($user->ID, 'first_name', true);
                    $last_name = get_user_meta($user->ID, 'last_name', true);
                    $country = get_user_meta($user->ID, 'country', true);
                    $email = $user->user_email;

                    // Get user roles and convert slugs to display names
                    $roles = array_map(function($role_slug) use ($role_names) {
                        return $role_names[$role_slug] ?? $role_slug;
                    }, $user->roles);
                    $roles_display = implode(', ', $roles);

                    // Fetch profile picture URL, or use a placeholder if not available
                    $profile_picture_link = get_user_profile_picture_link($user->ID);
                    if (!$profile_picture_link) {
                        $profile_picture_link = 'https://via.placeholder.com/50'; // Placeholder image URL
                    }
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo esc_url($profile_picture_link); ?>" alt="<?php esc_attr_e('Profile Picture', 'cool-kids-network'); ?>" class="profile-picture" />
                        </td>
                        <td><?php echo esc_html($first_name); ?></td>
                        <td><?php echo esc_html($last_name); ?></td>
                        <td><?php echo esc_html($country); ?></td>
                        <td><?php echo esc_html($email); ?></td>
                        <td><?php echo esc_html($roles_display); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}

/**
 * Fetches the user's profile picture URL.
 *
 * @param int $user_id The ID of the user.
 * @return string|false The URL of the profile picture, or false if not set.
 */
function get_user_profile_picture_link($user_id) {
    $attachment_id = get_user_meta($user_id, 'profile_picture_id', true);
    if ($attachment_id) {
        return wp_get_attachment_url($attachment_id);
    }
    return false; // Fallback if no profile picture is set
}
