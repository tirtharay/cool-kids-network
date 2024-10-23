<?php
class CoolKidsAdmin {

    private $user_list;
    private $demo_users_generator;

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);

        // Initialize the user list class (autoload will handle loading it)
        $this->user_list = new CoolKidsUserList();

        // Initialize the demo users generator class
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
            [$this, 'display_user_list'],  // Function to display the user list
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
            [$this, 'display_settings_page'] // Function to display the settings page (Demo users generation form)
        );
    }

    /**
     * Display the user list by calling the CoolKidsUserList class.
     */
    public function display_user_list() {
        $this->user_list->display_user_list(); // Call the method to display the user list
    }

    /**
     * Display the content for the Settings page (Generate Demo Users form).
     */
    public function display_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Cool Kids Network Settings', 'cool-kids-network'); ?></h1>
            <?php $this->demo_users_generator->display_form(); // Call the method to display the Generate Demo Users form ?>
        </div>
        <?php
    }
}
