<?php
/* 
 * Lost password Form
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="nswp-custom-registration-form">
	<div class="section-left">
		<p style="margin-bottom: 48px;font-size: 20px"> <?php echo __("You're one step closer to realizing the power of a digital presence.", 'woocommerce') ?></p>
		<p style="margin-bottom: 32px;font-size:20px"> <?php echo __('We at Holthuis Consult will help you take this vital step forward.', 'woocommerce') ?></p>
		<img class="site-logo" src="<?php echo get_site_url(). '/wp-content/uploads/2022/01/logo-Holthuis-consult.png' ?>" />
	</div>

	<div class="section-right" style="max-width: 580px">
		<h1 style="margin-bottom: 16px"><?php echo __( 'Reset password', 'wocommerce' ); ?></h1>
       
       <form method="post" class="woocommerce-ResetPassword lost_reset_password">

			<p class="woocommerce-form-row woocommerce-form-row--first form-row ">
				<label for="user_login"><?php esc_html_e( 'Email address', 'woocommerce' ); ?><span class="field-required-asterisk">*</span></label>
				<input style="width: 100%; background-color: white;" class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" autocomplete="username" />
			</p>

			<div class="clear"></div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>
        	<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'An email will be sent to this email address to reset the password if the email address is known to us.', 'woocommerce' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

        	<p style="display:flex: flex-direction: row;justify-content: space-between;">
                <span>
                	<span>Already have an account?</span> <a href="https://www.holthuisconsult.nl/login" style="text-decoration: underline">Sign in</a>
                </span>
            </p>

			<p class="woocommerce-form-row form-row">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button type="submit" class="woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?> submit-btn" value="<?php esc_attr_e( 'Reset password', 'woocommerce' ); ?>"><?php esc_html_e( 'Reset password', 'woocommerce' ); ?></button>
			</p>
			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

      	</form>
	</div>
</div>

<?php
do_action( 'woocommerce_after_lost_password_form' );


