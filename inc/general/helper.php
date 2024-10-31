<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_attribute_types' ) ) {

	function qode_variation_swatches_for_woocommerce_get_attribute_types() {
		$attribute_types = array(
			'select' => esc_html__( 'Select', 'qode-variation-swatches-for-woocommerce' ),
			'color'  => esc_html__( 'Color', 'qode-variation-swatches-for-woocommerce' ),
			'image'  => esc_html__( 'Image', 'qode-variation-swatches-for-woocommerce' ),
			'label'  => esc_html__( 'Label', 'qode-variation-swatches-for-woocommerce' ),
		);

		return array_merge( $attribute_types, apply_filters( 'qode_variation_swatches_for_woocommerce_filter_product_attributes_type', array() ) );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_attribute_value' ) ) {

	function qode_variation_swatches_for_woocommerce_get_attribute_value( $name, $attribute_id = 0 ) {
		$value = '';

		if ( ! empty( $attribute_id ) ) {
			$attribute_option_value = get_option( "$name-$attribute_id" );

			if ( '0' === $attribute_option_value || ! empty( $attribute_option_value ) ) {
				$value = $attribute_option_value;
			}
		}

		return $value;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_attribute_value_through_levels' ) ) {

	function qode_variation_swatches_for_woocommerce_get_attribute_value_through_levels( $name, $attribute_id = 0 ) {
		$value = '';

		$option_value = qode_variation_swatches_for_woocommerce_get_option_value( 'admin', $name );

		if ( '0' === $option_value || ! empty( $option_value ) ) {
			$value = $option_value;
		}

		if ( ! empty( $attribute_id ) ) {
			$attribute_option_value = get_option( "$name-$attribute_id" );

			if ( '0' === $attribute_option_value || ! empty( $attribute_option_value ) ) {
				$value = $attribute_option_value;
			}
		}

		return $value;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_original_term_id' ) ) {
	/**
	 * Get original product page ID if WPML plugin is installed
	 *
	 * @param int    $term_id  Term ID.
	 * @param string $taxonomy Taxonomy.
	 */
	function qode_variation_swatches_for_woocommerce_get_original_term_id( $term_id, $taxonomy ) {

		if ( ! empty( $term_id ) && ! empty( $taxonomy ) && qode_variation_swatches_for_woocommerce_is_installed( 'wpml' ) ) {
			global $sitepress;

			if ( ! empty( $sitepress ) && ! empty( $sitepress->get_default_language() ) ) {
				$term_id = apply_filters( 'wpml_object_id', $term_id, $taxonomy, false, $sitepress->get_default_language() );
			}
		}

		return absint( $term_id );
	}
}
