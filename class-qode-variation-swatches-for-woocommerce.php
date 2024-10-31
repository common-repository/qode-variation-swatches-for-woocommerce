<?php
/*
Plugin Name: QODE Variation Swatches for WooCommerce
Description: QODE Variation Swatches for WooCommerce provides you with a clear-cut way to present shoppers with detailed item variations alongside your products.
Author: Qode Interactive
Author URI: https://qodeinteractive.com/
Plugin URI: https://qodeinteractive.com/qode-variation-swatches-for-woocommerce/
Version: 1.0.5
Requires at least: 6.3
Requires PHP: 7.4
WC requires at least: 7.6
WC tested up to: 9.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: qode-variation-swatches-for-woocommerce
*/

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Variation_Swatches_For_WooCommerce' ) ) {
	class Qode_Variation_Swatches_For_WooCommerce {
		private static $instance;

		public function __construct() {
			// Set the main plugins constants.
			define( 'QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_PLUGIN_BASE_FILE', plugin_basename( __FILE__ ) );

			// Include required files.
			require_once __DIR__ . '/constants.php';
			require_once QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_ABS_PATH . '/helpers/helper.php';

			// Include framework file.
			require_once QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_ADMIN_PATH . '/class-qode-variation-swatches-for-woocommerce-framework.php';

			// Check if WooCommerce is installed.
			if ( function_exists( 'WC' ) ) {

				// Make plugin available for translation (permission 15 is set in order to be after the plugin initialization).
				add_action( 'plugins_loaded', array( $this, 'load_plugin_text_domain' ), 15 );

				// Add plugin's body classes.
				add_filter( 'body_class', array( $this, 'add_body_classes' ) );

				// Enqueue plugin's assets.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 11 );
				add_action( 'wp_enqueue_scripts', array( $this, 'add_inline_style' ), 11 );
				add_action( 'wp_enqueue_scripts', array( $this, 'localize_scripts' ), 11 );

				// Set WooCommerce features.
				add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_support' ) );

				// Include plugin's modules.
				$this->include_modules();
			}
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Variation_Swatches_For_WooCommerce
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function load_plugin_text_domain() {
			// Make plugin available for translation.
			load_plugin_textdomain( 'qode-variation-swatches-for-woocommerce', false, QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_REL_PATH . '/languages' );
		}

		public function add_body_classes( $classes ) {
			$classes[] = 'qode-variation-swatches-for-woocommerce-' . QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_VERSION;

			if ( wp_is_mobile() ) {
				$classes[] = 'qvsfw--touch';
			} else {
				$classes[] = 'qvsfw--no-touch';
			}

			return $classes;
		}

		public function add_inline_style() {
			$style = apply_filters( 'qode_variation_swatches_for_woocommerce_filter_add_inline_style', $style = '' );

			if ( ! empty( $style ) ) {
				wp_add_inline_style( 'qode-variation-swatches-for-woocommerce-main', $style );
			}
		}

		public function enqueue_assets() {
			// Enqueue CSS styles.
			wp_enqueue_style( 'qode-variation-swatches-for-woocommerce-main', QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/css/main.min.css', array(), QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_VERSION );

			// Enqueue JS scripts.
			wp_enqueue_script( 'qode-variation-swatches-for-woocommerce-main', QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/js/main.min.js', array( 'jquery' ), QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_VERSION, true );
		}

		public function localize_scripts() {
			$global = apply_filters(
				'qode_variation_swatches_for_woocommerce_filter_localize_main_plugin_script',
				array(
					'adminBarHeight' => is_admin_bar_showing() ? ( wp_is_mobile() ? 46 : 32 ) : 0,
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				)
			);

			wp_localize_script(
				'qode-variation-swatches-for-woocommerce-main',
				'qodeVariationSwatchesForWooCommerceGlobal',
				$global
			);
		}

		public function declare_wc_features_support() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_PLUGIN_BASE_FILE, true );
			}
		}

		public function include_modules() {
			// Hook to include additional element before modules inclusion.
			do_action( 'qode_variation_swatches_for_woocommerce_action_before_include_modules' );

			foreach ( glob( QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_INC_PATH . '/*/include.php' ) as $module ) {
				include_once $module;
			}

			// Hook to include additional element after modules inclusion.
			do_action( 'qode_variation_swatches_for_woocommerce_action_after_include_modules' );
		}
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_init_plugin' ) ) {
	/**
	 * Function that init plugin activation
	 */
	function qode_variation_swatches_for_woocommerce_init_plugin() {
		Qode_Variation_Swatches_For_WooCommerce::get_instance();
	}

	add_action( 'plugins_loaded', 'qode_variation_swatches_for_woocommerce_init_plugin' );
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_activation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin activation
	 */
	function qode_variation_swatches_for_woocommerce_activation_trigger() {
		// Hook to add additional code on plugin activation.
		do_action( 'qode_variation_swatches_for_woocommerce_action_on_activation' );
	}

	register_activation_hook( __FILE__, 'qode_variation_swatches_for_woocommerce_activation_trigger' );
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_deactivation_trigger' ) ) {
	/**
	 * Function that trigger hooks on plugin deactivation
	 */
	function qode_variation_swatches_for_woocommerce_deactivation_trigger() {
		// Hook to add additional code on plugin deactivation.
		do_action( 'qode_variation_swatches_for_woocommerce_action_on_deactivation' );
	}

	register_deactivation_hook( __FILE__, 'qode_variation_swatches_for_woocommerce_deactivation_trigger' );
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_check_requirements' ) ) {
	/**
	 * Function that check plugin requirements
	 */
	function qode_variation_swatches_for_woocommerce_check_requirements() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'qode_variation_swatches_for_woocommerce_admin_notice_content' );
		}
	}

	add_action( 'plugins_loaded', 'qode_variation_swatches_for_woocommerce_check_requirements' );
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_admin_notice_content' ) ) {
	/**
	 * Function that display the error message if the requirements are not met
	 */
	function qode_variation_swatches_for_woocommerce_admin_notice_content() {
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html__( 'WooCommerce plugin is required for QODE Variation Swatches for WooCommerce plugin to work properly. Please install/activate it first.', 'qode-variation-swatches-for-woocommerce' ) );
	}
}
