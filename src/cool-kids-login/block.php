<?php
/**
 * Renders the Cool Kids Login block.
 */
function render_cool_kids_login($attributes, $content) {
    // If the user is already logged in, redirect them to the profile page
    if (is_user_logged_in()) {
        $profile_page = get_page_by_path('profile');
        if ($profile_page) {
            wp_safe_redirect(get_permalink($profile_page->ID));
            exit; // Make sure to exit after redirection
        }
    } else {
        // If not logged in, render the login form
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ckn_email'])) {
            $email = sanitize_email($_POST['ckn_email']);
            $user = get_user_by('email', $email);

            if ($user) {
                // Log in the user
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);

                // Redirect to profile page after login
                $profile_page = get_page_by_path('profile-page');
                if ($profile_page) {
                    // Redirect the user ONLY after successful registration
                    wp_safe_redirect(get_permalink($profile_page->ID));
                    exit; // Important to stop further execution after redirection
                }
            } else {
                echo '<div class="login-error">Invalid email address. Please try again.</div>';
            }
        }

        // The HTML for the login form
        ob_start();
        ?>
        <div class="cool-kids-login-form">
            <form method="post">
                <input type="email" name="ckn_email" placeholder="Enter your email address" required>
                <button type="submit">Login</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
