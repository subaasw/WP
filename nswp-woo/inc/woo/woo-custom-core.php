<?php

if ( ! class_exists( 'NSWP_WC_Core' ) ) {

    class NSWP_WC_Core {

        public function __construct() {
            $this->load_core_functions();
        }

        public function load_core_functions() {
            add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            $this->maybe_reload_wc_cart();

            add_filter( 'woocommerce_cart_totals_coupon_label', [ $this, 'woocommerce_change_coupon_label' ] );
            add_filter( 'woocommerce_billing_fields', [ $this, 'ts_unrequire_wc_phone_field' ] );
            add_filter( 'woocommerce_checkout_place_order', [ $this, 'wc_custom_order_button_text' ] ); 
        }

        function ts_unrequire_wc_phone_field( $fields ) {
        
            $fields['billing_company']['required'] = true;
            $fields['billing_phone']['class'] = 'form-row-first';
            $fields['billing_email']['class'] = 'form-row-last';

            return $fields;
        }

        function wc_custom_order_button_text() {
            return __( 'Place order', 'woocommerce' ); 
        }

        function woocommerce_change_coupon_label( $arg ) {
            $code = explode( ' ', $arg );
            $code = $code[1] ? $code[1]: ' ';
            return __( 'Coupon code: ' . $code, 'woocommerce' );
        }

        private function maybe_reload_wc_cart() {
            if ( is_null( WC()->cart ) ) {
                wc_load_cart();
            }
        }

        public function rest_api_init() {
            $rest_api_namespace = 'nswp-api/v1';

            register_rest_route( $rest_api_namespace, '/cart-coupons', array(
                'methods' => 'GET',
                'callback' => array( $this, 'get_cart_coupons' ),
                'permission_callback' => '__return_true',
            ));

            register_rest_route( $rest_api_namespace, '/verify-coupon', array(
                'methods' => 'POST',
                'callback' => array( $this, 'custom_verify_coupon' ),
                'permission_callback' => '__return_true',
            ));

            register_rest_route( $rest_api_namespace, '/remove-coupon', array(
                'methods' => 'POST',
                'callback' => array( $this, 'remove_coupon' ),
                'permission_callback' => '__return_true',
            ));

            register_rest_route( $rest_api_namespace, '/cart/update_quantity', array(
                'methods'  => 'POST',
                'callback' => array( $this, 'update_cart_quantity' ),
                'permission_callback' => '__return_true',
            ) );
        }

        function update_cart_quantity(WP_REST_Request $request) {
            $product_id = $request->get_param('product_id');
            $action = $request->get_param('action');
            $quantity = $request->get_param('quantity') ? intval($request->get_param('quantity')) : 1;
        
            $cart = WC()->cart;
            $cart_item_key = $cart->find_product_in_cart($cart->generate_cart_id($product_id));
        
            if ($cart_item_key) {
                $current_quantity = $cart->get_cart_item($cart_item_key)['quantity'];
                
                if ($action === 'increment') {
                    $new_quantity = $current_quantity + $quantity;
                } elseif ($action === 'decrement') {
                    $new_quantity = max(1, $current_quantity - $quantity); // Ensure quantity doesn't go below 1
                } else {
                    return new WP_Error('invalid_action', 'Invalid action parameter. Use "increment" or "decrement".', array('status' => 400));
                }
                
                $cart->set_quantity($cart_item_key, $new_quantity);
            } else {
                if ($action === 'increment') {
                    $cart->add_to_cart($product_id, $quantity);
                } else {
                    return new WP_Error('no_cart_item', 'Product not in cart to decrement.', array('status' => 400));
                }
            }
        
            return rest_ensure_response(array('product_id' => $product_id, 'new_quantity' => $new_quantity));
        }        

        public function apply_coupon_code( $coupon_code ) {
            $cart = WC()->cart;

            if ( $cart->has_discount( $coupon_code ) ) {
                return false;
            }

            return $cart->apply_coupon( $coupon_code );
        }

        public function custom_verify_coupon( $request ) {
            $coupon_code = sanitize_text_field( $request->get_param( 'coupon_code' ) );

            if ( empty( $coupon_code ) ) {
                return new WP_Error( 'no_coupon_code', 'Coupon code is required', array( 'status' => 400 ) );
            }

            $coupon = new WC_Coupon( $coupon_code );

            $this->maybe_reload_wc_cart();

            if ( ! $coupon->get_id() ) {
                return new WP_Error( 'invalid_coupon', 'Invalid coupon code', array( 'status' => 404 ) );
            }

            // Apply the coupon code
            $apply_coupon_result = $this->apply_coupon_code( $coupon_code );

            if ( ! $apply_coupon_result ) {
                return new WP_Error( 'coupon_not_applied', 'Coupon could not be applied', array( 'status' => 401 ) );
            }

            $response = array(
                'code' => $coupon->get_code(),
                'amount' => $coupon->get_amount(),
                'currency_symbol' => get_woocommerce_currency_symbol()
            );

            return rest_ensure_response( $response );
        }

        public function get_cart_coupons() {
            $this->maybe_reload_wc_cart();

            $cart = WC()->cart;

            if ( ! $cart ) {
                return new WP_Error( 'cart_not_available', 'Cart is not available', array( 'status' => 500 ) );
            }

            // Get the applied coupons
            $coupons = $cart->get_applied_coupons();

            $coupons_details = array();

            foreach ( $coupons as $coupon_code ) {
                $coupon = new WC_Coupon( $coupon_code );
                $discount_amount = 0;

                // Calculate discount for the coupon
                foreach ( $cart->get_coupon_discount_totals() as $code => $amount ) {
                    if ( $coupon_code === $code ) {
                        $discount_amount = $amount;
                        break;
                    }
                }

                $coupons_details[] = array(
                    'code' => $coupon->get_code(),
                    'amount' => $discount_amount,
                    'currency_symbol' => get_woocommerce_currency_symbol()
                );
            }
 
            return rest_ensure_response( $coupons_details );
        }

        public function remove_coupon( $request ) {
            $coupon_code = sanitize_text_field( $request->get_param( 'coupon_code' ) );

            if ( empty( $coupon_code ) ) {
                return new WP_Error( 'no_coupon_code', 'Coupon code is required', array( 'status' => 400 ) );
            }

            $this->maybe_reload_wc_cart();

            $cart = WC()->cart;

            if ( ! $cart->has_discount( $coupon_code ) ) {
                return new WP_Error( 'coupon_not_found', 'Coupon code not found in cart', array( 'status' => 404 ) );
            }

            $res = $cart->remove_coupon( $coupon_code );

            $response = array(
                'message' => 'Coupon removed successfully',
		'success' => $res
            );

            return rest_ensure_response( $response );
        }

        public function enqueue_scripts() {
            wp_register_script( 'nswp-wc-script', NSWC_URL . '/assets/js/nswp-script.js', array( 'jquery' ), '1.0', true );

            wp_localize_script( 'nswp-wc-script', 'nswpRestApi', array(
                'url' => esc_url_raw( rest_url( 'nswp-api/v1' ) ),
                'nonce' => wp_create_nonce( 'nswp_rest' ),
            ));

            wp_enqueue_script( 'nswp-wc-script' );
        }
    }
}

function nswp_load_nswpcore() {
    new NSWP_WC_Core();
}

add_action( 'woocommerce_init', 'nswp_load_nswpcore' );
