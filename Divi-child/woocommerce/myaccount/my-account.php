<?php

 $current_user = wp_get_current_user();

 $customer_orders = wc_get_orders( 
    array(
        'customer_id' => $current_user->ID,
        'numberposts' => -1
        ) 
    );

echo '<div class="nswp-my-account-wrapper">';

echo '<h1>'. __('My account', 'woocommerce') . '</h1>';

echo '<h2 class="order-box">' . __( 'My orders', 'wocommerce' ) . '</h2> <br>';
echo '<div class="order-wrapper">';

if ( $customer_orders ) {
    echo '<table class="shop_table shop_table_responsive my_account_orders">';
    echo '<thead><tr>';
    echo '<th class="order-number">' . __( 'Order', 'wocommerce' ) . '</th>';
    echo '<th class="order-date">' . __( 'Date', 'wocommerce' ) . '</th>';
    echo '<th class="order-email">' . __( 'Email', 'wocommerce' ) . '</th>';
    echo '<th class="order-total">' . __( 'Total', 'wocommerce' ) . '</th>';
    echo '<th class="order-payment-method">' . __( 'Payment Method', 'wocommerce' ) . '</th>';
    echo '</tr></thead><tbody>';

    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order );
        $order_number = $order->get_order_number();
        $order_date = $order->get_date_created()->date_i18n();
        $order_email = $order->get_billing_email();
        $order_total = $order->get_formatted_order_total();
        $payment_method = $order->get_payment_method_title();
        $order_url = $order->get_checkout_order_received_url();

        echo '<tr>';
        echo '<td class="order-number"><a style="text-decoration: underline;" href="'. $order_url .'">' . $order_number . '</a></td>';
        echo '<td class="order-date">' . $order_date . '</td>';
        echo '<td class="order-email">' . $order_email . '</td>';
        echo '<td class="order-total">' . $order_total . '</td>';
        echo '<td class="order-payment-method">' . $payment_method . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}
else{
    echo "<p>No Orders Found</p>";
}
echo '</div>';

?>

    <div class="my-info">
        <h2 class='order-box' style="background-color: rgba(49, 111, 246, 0.21);"><?php _e('My information', 'woocommerce'); ?></h2>
        <div class="info-field">
            <p><strong><?php _e('First Name:', 'woocommerce'); ?></strong></p>
            <p><?php echo esc_html($current_user->first_name); ?></p>
        </div>

        <div class="info-field">
            <p><strong><?php _e('Last Name:', 'woocommerce'); ?></strong></p>
            <p><?php echo esc_html($current_user->last_name); ?></p>
        </div>

        <div class="info-field">
            <p><strong><?php _e('Email Address:', 'woocommerce'); ?></strong> 
            <p><?php echo esc_html($current_user->user_email); ?></p>
        </div>
    
    </div>

    <p><a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php _e('Reset password', 'woocommerce'); ?></a></p>
</div>

<style>
    .nswp-my-account-wrapper{
        max-width: 960px;
    }

    .nswp-my-account-wrapper h1,
    .nswp-my-account-wrapper h2,
    .nswp-my-account-wrapper h3,
    .nswp-my-account-wrapper p,
    .nswp-my-account-wrapper strong,
    .nswp-my-account-wrapper span{
        color: #000;
    }

    .nswp-my-account-wrapper h1{
        font-size: 24px;
        font-weight: 400;
    }

    .nswp-my-account-wrapper .order-box{
        background-color: rgba(217, 217, 217, 0.24);
        padding: 16px;
        font-size: 20px;
        font-weight: 400;
        display: inline-block;
        margin: 24px 0;
    }

    .nswp-my-account-wrapper .order-wrapper{
        margin-bottom: 128px
    }

    .my-info{
        margin-bottom: 64px;
    }

    .info-field{
        margin-bottom: 8px
    }

    .nswp-my-account-wrapper .my-info p{
    padding-bottom: 10px !important;
    }

</style>

<?php
