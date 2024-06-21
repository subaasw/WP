<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class NSWP_WooCommerce_Auth {

    public function __construct() {
        add_shortcode('nswp_wc_custom_registration_form', array($this, 'render_registration_form'));
        add_shortcode('nswp_wc_custom_login_form', array($this, 'render_login_form'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    public function render_registration_form() {
        ob_start();
        ?>
	<div class="nswp-custom-registration-form">
	<div class="section-left">
	<p style="margin-bottom: 48px;font-size: 20px"> <?php echo __("You're one step closer to realizing the power of a digital presence.", 'woocommerce') ?></p>
	<p style="margin-bottom: 32px;font-size:20px"> <?php echo __('We at Holthuis Consult will help you take this vital step forward.', 'woocommerce') ?></p>
	<img class="site-logo" src="<?php echo get_site_url(). '/wp-content/uploads/2022/01/logo-Holthuis-consult.png' ?>" />
	</div>
	<div class="section-right">
	<h1 style="margin-bottom: 16px"><?php echo __( 'Make an account', 'wocommerce' ); ?></h1>
        <form id="custom_registration_form">
	    <div class="field-name">
            <p>
                <label for="first_name">First Name<span class="field-required-asterisk">*</span></label>
                <input type="text" id="first_name" name="first_name" required>
            </p>
            <p>
                <label for="last_name">Last Name<span class="field-required-asterisk">*</span></label>
                <input type="text" id="last_name" name="last_name" required>
            </p>
	    </div>
            <p>
                <label for="email">Email<span class="field-required-asterisk">*</span></label>
                <input type="email" id="email" name="email" required>
            </p>
            <p>
                <label for="password">Password<span class="field-required-asterisk">*</span></label>
                <input type="password" id="password" name="password" required>
            </p>
            <p>
                <button type="submit">Sign Up</button>
            </p>
            <p style="flex-direction: row;">
                <span>Already have an account?</span> <a style="text-decoration: underline;" href="<?php echo esc_url(home_url('/login')); ?>">Sign in</a>
            </p>

        </form>
	</div>
	</div>
        <script>
            jQuery(document).ready(function($) {
                $('#custom_registration_form').on('submit', function(e) {
                    e.preventDefault();
                    var formData = {
                        first_name: $('#first_name').val(),
                        last_name: $('#last_name').val(),
                        email: $('#email').val(),
                        password: $('#password').val()
                    };
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo esc_url(home_url('/wp-json/nswp-api/v1/register')); ?>',
                        contentType: 'application/json',
                        data: JSON.stringify(formData),
                        success: function(response) {
                            if (response.success) {
                                window.location.href = '<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>';
                            } else {
                                alert(response.data);
                            }
                        }
                    });
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }

    public function render_login_form() {
        ob_start();
        ?>
	<div class="nswp-woo-login-wrapper">
	<div class="section-left">
	<p style="margin-bottom: 48px;font-size: 20px"> <?php echo __("You're one step closer to realizing the power of a digital presence.", 'woocommerce') ?></p>
	<p style="margin-bottom: 32px;font-size:20px"> <?php echo __('We at Holthuis Consult will help you take this vital step forward.', 'woocommerce') ?></p>
	<img class="site-logo" src="<?php echo get_site_url(). '/wp-content/uploads/2022/01/logo-Holthuis-consult.png' ?>" />
	</div>
	<div class="section-right">
	<h1><?php echo __( 'Sign in' , 'wocommerce' ); ?></h1>
        <form id="custom_login_form">
            <p>
                <label for="email">Email address<span class="field-required-asterisk">*</span></label>
                <input type="email" id="email" name="email" required>
            </p>
            <p>
                <label for="password">Password<span class="field-required-asterisk">*</span></label>
                <input type="password" id="password" name="password" required>
            </p>
            <p>
              <a style="align-self: flex-end;color: #707070;" href="<?php echo esc_url(wp_lostpassword_url()); ?>">Reset password</a>
            </p>
            <p>
                <button class="submit-btn" type="submit"><?php echo __( 'Sign in', 'woocommerce' ) ?></button>
            </p>
            <p style="flex-direction: row;justify-content: space-between;">
                <span>
                <span>Don't have an account?</span> <a href="<?php echo esc_url(home_url('/register')); ?>">Sign up</a>
                </span>
            </p>
            
        </form>
	</div>
        <script>
            jQuery(document).ready(function($) {
                $('#custom_login_form').on('submit', function(e) {
                    e.preventDefault();
                    var formData = {
                        email: $('#email').val(),
                        password: $('#password').val()
                    };
                    $.ajax({
                        type: 'POST',
                        url: '<?php echo esc_url(home_url('/wp-json/nswp-api/v1/login')); ?>',
                        contentType: 'application/json',
                        data: JSON.stringify(formData),
                        success: function(response) {
                            if (response.success) {
                                window.location.href = '<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>';
                            } else {
                                console.warn('Error:',response);
                            }
                        }
                    });
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }

    public function register_rest_routes() {
        register_rest_route('nswp-api/v1', '/register', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_registration'),
            'permission_callback' => '__return_true'
        ));
        register_rest_route('nswp-api/v1', '/login', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_login'),
            'permission_callback' => '__return_true'
        ));
    }

    public function handle_registration(WP_REST_Request $request) {
        $first_name = sanitize_text_field($request->get_param('first_name'));
        $last_name = sanitize_text_field($request->get_param('last_name'));
        $email = sanitize_email($request->get_param('email'));
        $password = sanitize_text_field($request->get_param('password'));

        // Validate form data
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            return new WP_Error('missing_fields', 'Please fill in all fields.', array('status' => 422));
        }

        // Check if the email is already registered
        if (email_exists($email)) {
            return new WP_Error('email_exists', 'This email is already registered.', array('status' => 400));
        }

        $user_data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $email,
            'user_login' => $email,
            'user_pass' => $password,
            'role' => 'customer'
        );

        $user_id = wp_insert_user($user_data);

        if ( is_wp_error( $user_id ) ) {
            return new WP_Error('registration_failed', $user_id->get_error_message(), array('status' => 400));
        }

        $wc_emails = new WC_Email_Customer_New_Account();
        $wc_emails->trigger($user_id, '', false);

        // Log the user in
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        return rest_ensure_response( array('success' => true) );
    }

    public function handle_login(WP_REST_Request $request) {
        $email = sanitize_email($request->get_param('email'));
        $password = sanitize_text_field($request->get_param('password'));

        if (empty($email) || empty($password)) {
            return new WP_Error('missing_fields', 'Please fill in all fields.', array('status' => 422));
        }

        $user = wp_authenticate($email, $password);

        if (is_wp_error($user)) {
            return new WP_Error('invalid_login', 'Invalid email or password.', array('status' => 401));
        }

        // Log the user in
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        return rest_ensure_response(array('success' => true));
    }
}

new NSWP_WooCommerce_Auth();

