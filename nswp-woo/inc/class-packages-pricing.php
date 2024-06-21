<?php

class NSWC_Pricing_Table{
    function __construct() {
        add_shortcode( 'nswc_product_gallery', array( $this, 'product_gallery_shortcode' ) );
        
        // ajax for price switcher
        add_action( 'wp_ajax_nopriv_pricing_table_switcher', array( $this, 'pricing_table_switcher_callback' ));
        add_action( 'wp_ajax_pricing_table_switcher', array( $this, 'pricing_table_switcher_callback'));
        
        // ajax for add to cart
        add_action( 'wp_ajax_nopriv_pricing-add-to-cart', array( $this, 'pricing_add_to_cart_callback' ));
        add_action( 'wp_ajax_pricing-add-to-cart', array( $this, 'pricing_add_to_cart_callback' ));

        // enqueue scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_nswc_scripts' ));
    }

    function enqueue_nswc_scripts() {
        wp_enqueue_style( 'nswc-custom-styles', NSWC_URL . 'assets/css/style.css', array(), '1.0', 'all' );

        wp_enqueue_script( 'nswc-custom-ajax-script', NSWC_URL . 'assets/js/script.js', array( 'jquery' ), '1.0', true );
        wp_localize_script( 'nswc-custom-ajax-script', 'ns_ajax_url',  array( 'url' => admin_url( 'admin-ajax.php' )) );
    }

    function pricing_add_to_cart_callback(){
        $product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';

        if ( ! $product_id ) wp_die();
        
        global $woocommerce;
        $res = $woocommerce->cart->add_to_cart( $product_id );

        wp_send_json( array( 'message'=> 'Added Successfully!', 'res' => $res ) );
    }

    function get_products_data( $category_slug ){
        $products_wc = wc_get_products( array('category' => $category_slug, 'return' => 'objects') );
        $currency_symbol = get_woocommerce_currency_symbol();

        $products_out = array();

        foreach ($products_wc as $product) {

            $categories = array();
            $categorie_ids = $product->get_category_ids();

            foreach ( $categorie_ids as $cat_id ){
                $category = get_term ( $cat_id, 'product_cat' );
                array_push($categories, $category->slug);
            }

            $product_details = array (
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'currency_symbol' => $currency_symbol,
                'desc' => $product->get_description(),
                'price' => $product->get_price(),
                'categories' => $categories
            );

            array_push( $products_out, $product_details );
        }

        $basic = [];
        $standard = [];
        $premium = [];

        foreach ( $products_out as $product ){

            if ( in_array( 'basic', $product['categories'] ) ){
                $basic = array_merge( ['short_name' => 'Basic'], $product );
            }
            else if ( in_array( 'standard', $product['categories'] ) ) {
                $standard = array_merge( ['short_name' => 'Standard'], $product );
            }
            else {
                $premium = array_merge( ['short_name' => 'Premium'], $product );
            }
        }

        $formatted_products = [];

        if ( $basic && $standard && $premium ){
            array_push( $formatted_products, $basic, $standard, $premium );
        }

        return $formatted_products;
    }

    function pricing_table_switcher_callback(){

        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';

        if ( ! $category ){
            wp_die();
            exit;
        }

        $products = $this->get_products_data($category);

        if ( ! count( $products ) ){
            $response = array(
                'success' => false,
                'message' => 'Failed to retrieved data',
                'products' => []
            );

            wp_send_json($response);
        }

        $response = array(
            'success' => true,
            'message' => 'Product data retrieved successfully.',
            'products' => $products
        );

        wp_send_json( $response );
    }

    function product_gallery_shortcode() {
        ob_start();

        $formatted_products = $this->get_products_data( 'monthly' ); ?>

        <div class="nswp-btn-wrapper">
            <div class="btns-switcher">
                <button class="pricing-btn-switcher active-btn" data-cat="monthly">Monthly</button>
                <button class="pricing-btn-switcher" data-cat="yearly">Yearly</button>
                <button class="pricing-btn-switcher" data-cat="3year">3 year</button>
            </div>
        </div>

        <?php 
        echo "<div class='nswp-pricing-container'>";
        foreach( $formatted_products as $product ) { 
            $image_url = "/wp-content/uploads/2024/04/House.png";
            
            if ( $product['short_name'] !== 'Basic' ){
                $image_url = $product['short_name'] === "Standard" ? "/wp-content/uploads/2024/04/Villa.png" : "/wp-content/uploads/2024/04/Castle.png";
            }
        ?>

            <div class="pricing-item-wrapper">
            <img src="<?php echo $image_url; ?>" alt="<?php echo $product['name']; ?>" />
                <h3><?php echo $product['short_name'] ?></h3>
                <p class="nswp-price-label">
                    <sup><?php echo $product['currency_symbol'] ?></sup> 
                    <span><?php echo $product['price']?></span>
                    <sub>/ month</sub>
                </p>
                <p class="font-sm"><?php echo $product['price'] * 12 ?> / year</p>

                <hr />
                <?php echo $product['desc'] ?> 
                <button class='ns-addToCart-btn' product-id='<?php echo $product['id'] ?>'>Add to cart</button>
            </div>

        <?php }
        echo "</div>";
        return ob_get_clean();
    }

}

new NSWC_Pricing_Table();
