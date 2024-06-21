<?php

class NSWP_Core{
    public function __construct(){
        add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_links' ) );
        add_action( 'template_redirect', array( $this, 'disable_woocommerce_pages' ) );
        add_action( 'template_redirect', array( $this, 'empty_cart_redirect' ) );

        $this->includes();
        // to remove permalink from package's title
        add_filter( 'woocommerce_cart_item_permalink','__return_false' );
    }

    function empty_cart_redirect(){
        if( is_cart() && WC()->cart->is_empty() ) {
            wp_safe_redirect( home_url( '/packages' ) );
            exit();
        }
    }

    function includes(){
        require_once NSWC_PATH . '/inc/woo/woo-custom-core.php';
        require_once NSWC_PATH . '/inc/woo/auth/class-nswp-custom-wc-auth.php';
    }

    function disable_woocommerce_pages() {

        if ( ! class_exists( 'WooCommerce' ) ) exit;

        if ( is_shop() || is_product() ) {
            // Redirect to home page or any other page you desire
            wp_redirect( home_url() );
            exit;
        }
    }

    function remove_admin_bar_links() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('view-store');
    }
}

new NSWP_Core();
