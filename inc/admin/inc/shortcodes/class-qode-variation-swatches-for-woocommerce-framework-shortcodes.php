<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

class Qode_Variation_Swatches_For_WooCommerce_Framework_Shortcodes {
	private $shortcodes = array();

	public function __construct() {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_GET['elementor_updater'] ) && 'continue' === sanitize_text_field( wp_unslash( $_GET['elementor_updater'] ) ) ) {
			if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>' ) ) {
				// Permission 5 is set in order to include shortcode files before register '-elementor.php' files.
				add_action( 'elementor/widgets/register', array( $this, 'register' ), 5 );
			} else {
				// Permission 5 is set in order to include shortcode files before register '-elementor.php' files.
				add_action( 'elementor/widgets/widgets_registered', array( $this, 'register' ), 5 );
			}
		} else {
			// Permission 0 is set in order to register shortcodes before widgets, because widgets using shortcodes options.
			add_action( 'init', array( $this, 'register' ), 0 );
		}
	}

	public function get_shortcodes() {
		return $this->shortcodes;
	}

	public function set_shortcodes( $base, $shortcode ) {
		$this->shortcodes[ $base ] = $shortcode;
	}

	public function get_shortcode( $base ) {
		$shortcodes = $this->get_shortcodes();

		if ( ! empty( $shortcodes ) && isset( $shortcodes[ $base ] ) ) {
			return $shortcodes[ $base ];
		}

		return false;
	}

	private function set_shortcode( Qode_Variation_Swatches_For_WooCommerce_Framework_Shortcode $shortcode ) {
		$this->set_shortcodes( $shortcode->get_base(), $shortcode );
	}

	public function shortcode_exists( $base ) {
		return array_key_exists( $base, $this->get_shortcodes() );
	}

	public function add_shortcode( Qode_Variation_Swatches_For_WooCommerce_Framework_Shortcode $shortcode ) {
		$key = $shortcode->get_base();

		if ( ! empty( $key ) ) {
			$this->set_shortcode( $shortcode );

			return $shortcode;
		}

		return false;
	}

	public function register() {
		do_action( 'qode_variation_swatches_for_woocommerce_action_framework_before_shortcodes_register' );

		$shortcodes = $this->get_shortcodes();

		if ( ! empty( $shortcodes ) && is_array( $shortcodes ) ) {
			ksort( $shortcodes );

			foreach ( $shortcodes as $shortcode ) {
				$shortcode->register();
			}
		}

		do_action( 'qode_variation_swatches_for_woocommerce_action_framework_after_shortcodes_register' );
	}
}
