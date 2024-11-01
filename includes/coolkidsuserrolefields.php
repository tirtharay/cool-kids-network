<?php
/**
 * Cool Kids User Role Fields class
 * This class handles the "User Role Fields" settings page for controlling field visibility in the User Directory.
 */
class CoolKidsUserRoleFields
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_user_role_fields_page']);
    }

    /**
     * Add the "User Role Fields" page under the "Cool Kids Network" admin menu.
     */
    public function add_user_role_fields_page()
    {
        add_submenu_page(
            'cool-kids-network',                // Parent slug
            'User Role Fields',                 // Page title
            'User Role Fields',                 // Menu title
            'manage_options',                   // Capability
            'cool-kids-user-role-fields',       // Menu slug
            [$this, 'display_user_role_fields_page'] // Callback function
        );
    }

    /**
     * Display the content for the "User Role Fields" page.
     */
    public function display_user_role_fields_page()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->save_role_field_settings();
        }

        // Get current settings
        $fields = ['first_name', 'last_name', 'email', 'country', 'role'];
        $roles = ['cool_kid', 'cooler_kid', 'coolest_kid'];

        ?>
        <div class="wrap">
            <h1><?php esc_html_e('User Role Fields', 'cool-kids-network'); ?></h1>
            <p><?php esc_html_e('Control the fields each role can see in the User Directory.', 'cool-kids-network'); ?></p>
            <form method="post">
                <?php foreach ($roles as $role) : ?>
                    <h2><?php echo ucfirst(str_replace('_', ' ', $role)); ?> Permissions</h2>
                    <?php foreach ($fields as $field) : ?>
                        <?php
                        $option_name = "{$role}_show_{$field}";
                        $checked = get_option($option_name, 'no') === 'yes' ? 'checked' : '';
                        ?>
                        <label>
                            <input type="checkbox" name="<?php echo esc_attr($option_name); ?>" value="yes" <?php echo esc_attr($checked); ?>>
                            <?php echo ucfirst(str_replace('_', ' ', $field)); ?>
                        </label><br>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <input type="submit" value="<?php esc_attr_e('Save Settings', 'cool-kids-network'); ?>" class="button button-primary">
            </form>
        </div>
        <?php
    }

    /**
     * Save the role field visibility settings.
     */
    private function save_role_field_settings()
    {
        $fields = ['first_name', 'last_name', 'email', 'country', 'role'];
        $roles = ['cool_kid', 'cooler_kid', 'coolest_kid'];

        foreach ($roles as $role) {
            foreach ($fields as $field) {
                $option_name = "{$role}_show_{$field}";
                update_option($option_name, isset($_POST[$option_name]) ? 'yes' : 'no');
            }
        }

        echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'cool-kids-network') . '</p></div>';
    }
}
