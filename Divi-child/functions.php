<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( ! function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;

add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

// END ENQUEUE PARENT ACTION
add_action('wp_enqueue_scripts', 'enqueue_divi_child_theme_styles');

function enqueue_divi_child_theme_styles() {
    wp_enqueue_style( 'ns-divi-child-style', get_stylesheet_directory_uri() . '/style.css', array(), '1.0.0', 'all' );
}

function ns_check_is_script_enqueued( $script_handle ){
	return wp_script_is( $script_handle, 'enqueued' );
}

function ns_enqueue_scripts(){

	 // Enqueue the JavaScript file
    if ( ! ns_check_is_script_enqueued( 'ns-carousel-min-script' )){
		  wp_enqueue_script('ns-carousel-min-script', get_theme_file_uri('assets/js/ns-carousel-min.js'), array(), '1.0',false);
	  }
	
    if ( ! ns_check_is_script_enqueued( 'ns-carousel-custom-script' )){
      wp_register_script('ns-carousel-custom-script', get_theme_file_uri( 'assets/js/ns-carousel-custom.js' ), array(), '1.0');
      wp_enqueue_script('ns-carousel-custom-script');

      add_filter( "script_loader_tag", "add_module_to_my_script", 10, 3 );
      function add_module_to_my_script($tag, $handle, $src) {
        if ( "ns-carousel-custom-script" === $handle ) {
          $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        }

        return $tag;
      }
    }
}

function ns_carousel_wp_shortcode_function() {
	ns_enqueue_scripts();
    ob_start(); ?>

	<section class="embla">
      <div class="embla__viewport">
        <div class="embla__container">
          <div class="embla__slide">
            <div class="embla__slide__content">
              <p class="ns-font-400 ns-font-24px">Customer 1</p>
              <p class="ns-font-400">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
                enim ad minim veniam, quis nostrud exercitation ullamco laboris
                nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                in reprehenderit in voluptate velit esse cillum
              </p>
            </div>
            <div class="ns__embla__avatar"></div>
          </div>
          <div class="embla__slide">
            <div class="embla__slide__content">
              <p class="ns-font-400 ns-font-24px">Customer 2</p>
              <p class="ns-font-400">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
                enim ad minim veniam, quis nostrud exercitation ullamco laboris
                nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                in reprehenderit in voluptate velit esse cillum
              </p>
            </div>
            <div class="ns__embla__avatar"></div>
          </div>
          <div class="embla__slide">
            <div class="embla__slide__content">
              <p class="ns-font-400 ns-font-24px">Customer 3</p>
              <p class="ns-font-400">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
                enim ad minim veniam, quis nostrud exercitation ullamco laboris
                nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                in reprehenderit in voluptate velit esse cillum
              </p>
            </div>
            <div class="ns__embla__avatar"></div>
          </div>
          <div class="embla__slide">
            <div class="embla__slide__content">
              <p class="ns-font-400 ns-font-24px">Customer 4</p>
              <p class="ns-font-400">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
                enim ad minim veniam, quis nostrud exercitation ullamco laboris
                nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                in reprehenderit in voluptate velit esse cillum
              </p>
            </div>
            <div class="ns__embla__avatar"></div>
          </div>
          <div class="embla__slide">
            <div class="embla__slide__content">
              <p class="ns-font-400 ns-font-24px">Customer 5</p>
              <p class="ns-font-400">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut
                enim ad minim veniam, quis nostrud exercitation ullamco laboris
                nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor
                in reprehenderit in voluptate velit esse cillum
              </p>
            </div>
            <div class="ns__embla__avatar"></div>
          </div>
        </div>
      </div>

      <div class="embla__dots"></div>
    </section>
	
	<?php 
	$output = ob_get_clean();

  // Return the output of the shortcode
  return $output;
}
add_shortcode( 'ns-carousel-wp', 'ns_carousel_wp_shortcode_function' );
