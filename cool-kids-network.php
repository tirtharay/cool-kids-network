<?php
/**
 * Plugin Name: Cool Kids Network
 * Description: A plugin for managing custom user roles and login functionality for the Cool Kids Network.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: cool-kids-network
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CoolKidsNetwork {

    public function __construct() {
        // Register hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_action('init', [$this, 'register_blocks']);

        // Add profile picture field before the username field
        add_action('personal_options', [$this, 'display_profile_picture_field']);

        // Redirect logged-in users if they try to access the login or signup page
        add_action('template_redirect', [$this, 'redirect_logged_in_users']);
    }

    public function activate() {
        $this->create_roles();
        $this->set_permalinks();
    }

    public function deactivate() {
        $this->remove_roles();
    }

    private function create_roles() {
        $capabilities = [
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => false,
        ];

        add_role('cool_kid', 'Cool Kid', $capabilities);
        add_role('maintainer', 'Maintainer', $capabilities);
        add_role('cooler_kid', 'Cooler Kid', $capabilities);
        add_role('coolest_kid', 'Coolest Kid', $capabilities);

        update_option('default_role', 'cool_kid');
    }

    private function remove_roles() {
        remove_role('cool_kid');
        remove_role('maintainer');
        remove_role('cooler_kid');
        remove_role('coolest_kid');

        update_option('default_role', 'subscriber');
    }

    private function set_permalinks() {
        update_option('permalink_structure', '/%postname%/');
        flush_rewrite_rules();
    }

    public function register_blocks() {
        $blocks = [
            'cool-kids-profile'
        ];

        foreach ($blocks as $block) {
            $block_path = plugin_dir_path(__FILE__) . 'build/' . $block;
            $block_php_path = plugin_dir_path(__FILE__) . 'src/' . $block . '/block.php';

            // Check if block.php exists before including it
            if (file_exists($block_php_path)) {
                require_once $block_php_path;

                // Dynamically determine the render callback function name based on the block name
                $function_name = 'render_' . str_replace('-', '_', $block);

                register_block_type($block_path, [
                    'render_callback' => $function_name
                ]);
            } else {
                register_block_type($block_path);
            }
        }
    }

    // Function to display the profile picture in the user profile above the username field
    public function display_profile_picture_field($user) {
        // Get the user's profile picture URL using your existing function
        $profile_picture_url = $this->get_user_profile_picture_url($user->ID);
        ?>
        <h3><?php _e('Profile Picture', 'cool-kids-network'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="profile_picture"><?php _e('Profile Picture', 'cool-kids-network'); ?></label></th>
                <td>
                    <?php if ($profile_picture_url): ?>
                        <img src="<?php echo esc_url($profile_picture_url); ?>" alt="<?php _e('User Profile Picture', 'cool-kids-network'); ?>" style="max-width: 150px; height: auto;" />
                    <?php else: ?>
                        <p><?php _e('No profile picture set.', 'cool-kids-network'); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        <?php
    }

    // Function to get the user's profile picture URL from the user meta
    public function get_user_profile_picture_url($user_id) {
        $attachment_id = get_user_meta($user_id, 'profile_picture_id', true);
        if ($attachment_id) {
            return wp_get_attachment_url($attachment_id);
        }
        return false; // Fallback if no profile picture is set
    }

    // Redirect logged-in users if they try to access login or signup pages
    public function redirect_logged_in_users() {
        if (is_user_logged_in()) {
            global $post;

            // Define the login and signup page slugs (adjust if needed)
            $login_slug = 'login-page';
            $signup_slug = 'signup-page';

            // Check if the user is on the login or signup page
            if ($post && in_array($post->post_name, [$login_slug, $signup_slug])) {
                $profile_page = get_page_by_path('profile-page');
                if ($profile_page) {
                    wp_redirect(get_permalink($profile_page->ID));
                    exit;
                }
            }
        }
    }
}

new CoolKidsNetwork();
