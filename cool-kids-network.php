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

            if ($block === 'cool-kids-profile') {
                // Include the block.php file for the profile block
                require_once plugin_dir_path(__FILE__) . 'src/' . $block . '/block.php';

                register_block_type($block_path, [
                    'render_callback' => 'render_cool_kids_profile'
                ]);
            } else {
                register_block_type($block_path);
            }
        }
    }

}

new CoolKidsNetwork();
