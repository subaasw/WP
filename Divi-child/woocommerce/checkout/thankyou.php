<?php

/**
 * Display WooCommerce order details using order key from POST request.
 */

function display_order_details_by_post_request() {
    // Check if the key is present in the POST data
    if ( isset( $_GET['key'] ) ) {
        $order_key = sanitize_text_field( $_GET['key'] );
        
        // Get order object
        $order_id = wc_get_order_id_by_order_key( $order_key );
        $order = wc_get_order( $order_id );

        // Check if order exists
	    echo '<div class="order-recieved-wrapper">';
        if ( ! $order ) {
            echo '<p>' . __( 'Order not found.', 'wocommerce' ) . '</p>';
            return;
        }

        echo '<h1 class="confirmation">Confirmation</h1>';
        echo '<h2 class="thank-you">' . __( 'Thank you, your order has been received!', 'wocommerce' ) . '</h2>';

        // Display order details
        echo "<table class='nswp-order-table'><thead><tr>";
        echo '<th>' . __( 'Order number', 'wocommerce' ) . '</th>';
        echo '<th>' . __( 'Date', 'wocommerce' ) . '</th>';
        echo '<th>' . __( 'Email', 'wocommerce' ) . '</th>';
        echo '<th>' . __( 'Total', 'wocommerce' ) . '</th>';
        echo '<th>' . __( 'Payment method', 'wocommerce' ) . '</th></tr></thead>';

        echo '<tbody><td>' . $order->get_order_number() . '</td>';
        echo '<td>' . $order->get_date_created()->date_i18n() . '</td>';
        echo '<td>' . $order->get_billing_email() . '</td>';
        echo '<td>' . $order->get_formatted_order_total() . '</td>';
        echo '<td>' . $order->get_payment_method_title() . '</td></tr>';
        echo "</tbody></table>";

        echo '<strong class="email">'. __( 'A confirmation email will be sent to ', 'wocommerce' ) . $order->get_billing_email() . __( ' with further details.', 'wocommerce' ). '</strong>';

        ?>

        <div class="nswp-flex-details">
            <div class="order-items-mini-table">
                <h3><?php echo __( 'Order details', 'wocommerce' ) ?></h3>
                <?php
                // Display order items
            $items = $order->get_items();
        
            echo '<table>';
            echo '<thead><tr><th>' . __( 'Product', 'wocommerce' ) . '</th><th>' . __( 'Subtotal', 'wocommerce' ) . '</th></tr></thead>';
            echo '<tbody>';
            foreach ( $items as $item_id => $item ) {
                $product_name = $item->get_name();
                $product_price = wc_price( $item->get_total() );
                echo '<tr><td>' . $product_name . '</td><td>' . $product_price . '</td></tr>';
            }
            $coupons = $order->get_coupon_codes();
            if ( ! empty( $coupons ) ) {

                foreach ( $coupons as $coupon_code ) {
                    $coupon = new WC_Coupon( $coupon_code );
                    $discount_amount = wc_price( $order->get_discount_total() );
                    echo '<tr><td> Coupon code: ' . $coupon_code . '</td>';
                    echo '<td>' . $discount_amount . '</td></tr>';
                }
            }
            // Display order total
            echo '<tr style="border-top: 2px solid #000 !important;"><th>' . __( 'Order Total:', 'wocommerce' ) . '</th> <td>' . $order->get_formatted_order_total() . '</td></tr>';

            echo '</tbody></table>';

        } else {
            echo '<p>' . __( 'Sorry no order found!', 'wocommerce' ) . '</p>';
        } ?>

            </div>
            <div class="billing-wrapper">
            <h3><?php echo __( 'Billing address', 'wocommerce' ) ?></h3>
            <div>
                <?php
                    echo '<p>' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</p>';
                    echo '<p>' . $order->get_billing_country() . ' ' . $order->get_billing_city() . ' '. $order->get_billing_postcode() . '</p>';
                    echo '<p>' . $order->get_billing_phone() . '</p>';
                    echo '<p>' . $order->get_billing_email() . '</p>';
                ?>
            </div>
            </div>
        </div>
    </div>
<?php
}

// Call the function
display_order_details_by_post_request();
