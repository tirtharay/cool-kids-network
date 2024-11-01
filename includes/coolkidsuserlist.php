<?php
/**
 * Cool Kids User List class
 * This class handles the display of users with specific roles in the admin panel.
 */
class CoolKidsUserList
{

    public function __construct()
    {
        // You can add hooks or other initialization here if needed.
    }

    /**
     * Display the user list for users with roles "Cool Kid", "Cooler Kid", and "Coolest Kid".
     */
    public function display_user_list()
    {
        // Get the selected role from the query parameters
        $selected_role = isset($_GET['user_role']) ? sanitize_text_field($_GET['user_role']) : '';

        // Define the available roles for filtering
        $roles = [
            'cool_kid' => 'Cool Kid',
            'cooler_kid' => 'Cooler Kid',
            'coolest_kid' => 'Coolest Kid',
            'maintainer' => 'Maintainer',
        ];

        // Build the WP_User_Query arguments
        $args = [
            'role__in' => array_keys($roles),  // Get users with all roles by default
            'orderby' => 'display_name',
            'order' => 'ASC',
            'number' => 100,       // Number of users per page
        ];

        // If a specific role is selected, filter by that role
        if (!empty($selected_role) && array_key_exists($selected_role, $roles)) {
            $args['role'] = $selected_role;
        }

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Cool Kids Network Users', 'cool-kids-network', 'maintainer'); ?></h1>

            <!-- Filter form -->
            <form method="get" action="">
                <input type="hidden" name="page" value="cool-kids-network">
                <label for="user_role"><?php esc_html_e('Filter by Role:', 'cool-kids-network'); ?></label>
                <select name="user_role" id="user_role">
                    <option value=""><?php esc_html_e('All Roles', 'cool-kids-network'); ?></option>
                    <?php foreach ($roles as $role_value => $role_name) : ?>
                        <option value="<?php echo esc_attr($role_value); ?>" <?php selected($selected_role, $role_value); ?>>
                            <?php echo esc_html($role_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button"><?php esc_html_e('Filter', 'cool-kids-network'); ?></button>
            </form>

            <!-- User List -->
            <?php if (!empty($users)) : ?>
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Full Name', 'cool-kids-network'); ?></th>
                            <th><?php esc_html_e('Email', 'cool-kids-network'); ?></th>
                            <th><?php esc_html_e('Role', 'cool-kids-network'); ?></th>
                            <th><?php esc_html_e('Profile Picture', 'cool-kids-network'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></td>
                                <td><?php echo esc_html($user->user_email); ?></td>
                                <td><?php echo esc_html(ucwords(str_replace('_', ' ', $user->roles[0]))); ?></td>
                                <td>
                                    <?php
                                    // Get the profile picture URL
                                    $profile_picture_url = get_user_meta($user->ID, 'profile_picture_id', true);
                                    if ($profile_picture_url) {
                                        $profile_picture_url = wp_get_attachment_url($profile_picture_url);
                                        echo '<img src="' . esc_url($profile_picture_url) . '" alt="' . esc_attr($user->display_name) . '" style="max-width: 50px; height: auto;" />';
                                    } else {
                                        echo esc_html__('No picture available', 'cool-kids-network');
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('No users found with the specified roles.', 'cool-kids-network'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}
