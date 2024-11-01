<?php
/**
 * Renders the Cool Kids User Directory block.
 */
function render_cool_kids_user_directory($attributes, $content) {
    // Get current user (viewer)
    $current_user = wp_get_current_user();
    $viewer_role = $current_user->roles[0];

    // Check if the current user is an admin or maintainer
    $is_admin_or_maintainer = in_array('administrator', $current_user->roles) || in_array('maintainer', $current_user->roles);

    // Query users with roles 'Cool Kid', 'Cooler Kid', 'Coolest Kid'
    $user_query = new WP_User_Query([
        'role__in' => ['cool_kid', 'cooler_kid', 'coolest_kid']
    ]);

    // Check if there are users found
    if (empty($user_query->get_results())) {
        return '<p>No users found.</p>';
    }

    // Define the mapping of role slugs to role names
    $role_names = [
        'cool_kid' => 'Cool Kid',
        'cooler_kid' => 'Cooler Kid',
        'coolest_kid' => 'Coolest Kid',
    ];

    // Start output buffering
    ob_start();
    ?>
    <table class="cool-kids-user-directory">
        <thead>
            <tr>
                <th><?php esc_html_e('Profile Picture', 'cool-kids-network'); ?></th>
                <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_first_name") === 'yes') : ?>
                    <th><?php esc_html_e('First Name', 'cool-kids-network'); ?></th>
                <?php endif; ?>
                <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_last_name") === 'yes') : ?>
                    <th><?php esc_html_e('Last Name', 'cool-kids-network'); ?></th>
                <?php endif; ?>
                <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_country") === 'yes') : ?>
                    <th><?php esc_html_e('Country', 'cool-kids-network'); ?></th>
                <?php endif; ?>
                <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_email") === 'yes') : ?>
                    <th><?php esc_html_e('Email', 'cool-kids-network'); ?></th>
                <?php endif; ?>
                <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_role") === 'yes') : ?>
                    <th><?php esc_html_e('Role', 'cool-kids-network'); ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($user_query->get_results() as $user) : ?>
                <?php
                // Get user profile information
                $first_name = get_user_meta($user->ID, 'first_name', true);
                $last_name = get_user_meta($user->ID, 'last_name', true);
                $country = get_user_meta($user->ID, 'country', true);
                $email = $user->user_email;
                $user_roles = $user->roles;

                // Get profile picture URL
                $profile_picture_url = get_user_profile_picture_url($user->ID);
                if (!$profile_picture_url) {
                    $profile_picture_url = 'https://via.placeholder.com/50';
                }
                ?>
                <tr>
                    <td><img src="<?php echo esc_url($profile_picture_url); ?>" alt="<?php esc_attr_e('Profile Picture', 'cool-kids-network'); ?>" class="profile-picture" /></td>

                    <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_first_name") === 'yes') : ?>
                        <td><?php echo esc_html($first_name); ?></td>
                    <?php endif; ?>

                    <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_last_name") === 'yes') : ?>
                        <td><?php echo esc_html($last_name); ?></td>
                    <?php endif; ?>

                    <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_country") === 'yes') : ?>
                        <td><?php echo esc_html($country); ?></td>
                    <?php endif; ?>

                    <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_email") === 'yes') : ?>
                        <td><?php echo esc_html($email); ?></td>
                    <?php endif; ?>

                    <?php if ($is_admin_or_maintainer || get_option("{$viewer_role}_show_role") === 'yes') : ?>
                        <td><?php echo esc_html(implode(', ', array_map(function($role) use ($role_names) {
                            return $role_names[$role] ?? ucfirst($role);
                        }, $user_roles))); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}
