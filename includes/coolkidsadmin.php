<?php
/**
 * Cool Kids Admin class
 * This class handles the creation of the admin menu for the Cool Kids Network plugin.
 */
class CoolKidsAdmin {

    //Import classes
    private $demo_users_generator;

    public function __construct() {

        add_action('admin_menu', [$this, 'add_admin_menu']);
        // Initialize the demo users generator
        add_action('admin_menu', [$this, 'add_admin_menu']);
        $this->demo_users_generator = new CoolKidsDemoUsersGenerator();
    }

    /**
     * Add the admin menu for Cool Kids Network.
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            'Cool Kids Network',  // Page title
            'Cool Kids Network',  // Menu title
            'manage_options',     // Capability required
            'cool-kids-network',  // Menu slug
            [$this, 'display_settings_page'],  // Function to display the page content
            'dashicons-groups',   // Icon
            6                     // Position
        );

        // Submenu: Settings
        add_submenu_page(
            'cool-kids-network',  // Parent slug
            'Settings',           // Page title
            'Settings',           // Menu title
            'manage_options',     // Capability required
            'cool-kids-network-settings', // Menu slug
            [$this, 'display_settings_page'] // Function to display the page content
        );
    }

    /**
     * Display the content for the Settings page.
     */
    public function display_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Cool Kids Network Settings', 'cool-kids-network'); ?></h1>

            <?php if (isset($_GET['demo_users_generated'])): ?>
                <div class="notice notice-success">
                    <p><?php printf(esc_html__('%d demo users were successfully created.', 'cool-kids-network'), intval($_GET['demo_users_generated'])); ?></p>
                </div>
            <?php endif; ?>

            <?php
            // Display the demo users generator form
            $this->demo_users_generator->display_form();
            ?>
        </div>
        <?php
    }
}
