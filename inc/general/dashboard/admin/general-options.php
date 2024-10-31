<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_add_general_options' ) ) {
	/**
	 * Function that add general options for this module
	 */
	function qode_variation_swatches_for_woocommerce_add_general_options( $page ) {

		if ( $page ) {

			$welcome_section = $page->add_section_element(
				array(
					'layout'      => 'welcome',
					'name'        => 'qode_variation_swatches_for_woocommerce_global_plugins_options_welcome_section',
					'title'       => esc_html__( 'Welcome to Qode Variation Swatches for WooCommerce', 'qode-variation-swatches-for-woocommerce' ),
					'description' => esc_html__( 'It\'s time to set up the Variation Swatches feature on your website', 'qode-variation-swatches-for-woocommerce' ),
					'icon'        => QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_ASSETS_URL_PATH . '/img/icon.png',
				)
			);

			// Hook to include additional options after module options.
			do_action( 'qode_variation_swatches_for_woocommerce_action_before_general_options_map', $page );

			if ( ! qode_variation_swatches_for_woocommerce_is_installed( 'variation-swatches-premium' ) ) {

				$page->add_field_element(
					array(
						'field_type' => 'text',
						'name'       => 'qode_variation_swatches_for_woocommerce_variation_swatches_min_width',
						'title'      => esc_html__( 'Variation Swatches Width', 'qode-variation-swatches-for-woocommerce' ),
					)
				);

				$page->add_field_element(
					array(
						'field_type' => 'text',
						'name'       => 'qode_variation_swatches_for_woocommerce_variation_swatches_height',
						'title'      => esc_html__( 'Variation Swatches Height', 'qode-variation-swatches-for-woocommerce' ),
					)
				);

				$page->add_field_element(
					array(
						'field_type' => 'color',
						'name'       => 'qode_variation_swatches_for_woocommerce_variation_swatches_border_color',
						'title'      => esc_html__( 'Variation Swatches Border Color', 'qode-variation-swatches-for-woocommerce' ),
					)
				);

				$page->add_field_element(
					array(
						'field_type' => 'color',
						'name'       => 'qode_variation_swatches_for_woocommerce_variation_swatches_selected_border_color',
						'title'      => esc_html__( 'Selected Variation Swatches Border Color', 'qode-variation-swatches-for-woocommerce' ),
					)
				);

				$page->add_field_element(
					array(
						'field_type'  => 'text',
						'name'        => 'qode_variation_swatches_for_woocommerce_space_between_variation_swatches',
						'title'       => esc_html__( 'Space Between Variation Swatches', 'qode-variation-swatches-for-woocommerce' ),
						'description' => esc_html__( 'Adjust the size of the space between items in pixels', 'qode-variation-swatches-for-woocommerce' ),
					)
				);

				do_action( 'qode_variation_swatches_for_woocommerce_action_framework_after_options_map', $page );
			}
		}
	}

	add_action( 'qode_variation_swatches_for_woocommerce_action_default_options_init', 'qode_variation_swatches_for_woocommerce_add_general_options' );
}
