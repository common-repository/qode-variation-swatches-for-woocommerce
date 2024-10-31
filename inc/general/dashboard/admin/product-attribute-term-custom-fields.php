<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_add_product_attribute_terms_options' ) ) {
	/**
	 * Function that add global taxonomy options for current module
	 */
	function qode_variation_swatches_for_woocommerce_add_product_attribute_terms_options() {

		$qode_framework = qode_variation_swatches_for_woocommerce_framework_get_framework_root();

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {

				$page = $qode_framework->add_options_page(
					array(
						'scope' => array( 'pa_' . $tax->attribute_name ),
						'type'  => 'taxonomy',
						'slug'  => 'pa_' . $tax->attribute_name,
					)
				);

				if ( $page ) {

					$page->add_section_element(
						array(
							'layout' => 'product-attribute',
							'name'   => 'qode_variation_swatches_for_woocommerce_product_taxonomy_section_title',
							'title'  => esc_html__( 'Qode Variation Swatches Options', 'qode-variation-swatches-for-woocommerce' ),
						)
					);

					if ( 'color' === $tax->attribute_type ) {

						$page->add_field_element(
							array(
								'field_type' => 'color',
								'name'       => 'qode_variation_swatches_for_woocommerce_color',
								'title'      => esc_html__( 'Color', 'qode-variation-swatches-for-woocommerce' ),
							)
						);
					} elseif ( 'image' === $tax->attribute_type ) {

						$page->add_field_element(
							array(
								'field_type' => 'image',
								'name'       => 'qode_variation_swatches_for_woocommerce_image',
								'title'      => esc_html__( 'Image', 'qode-variation-swatches-for-woocommerce' ),
							)
						);
					} elseif ( 'label' === $tax->attribute_type ) {

						$page->add_field_element(
							array(
								'field_type' => 'text',
								'name'       => 'qode_variation_swatches_for_woocommerce_label',
								'title'      => esc_html__( 'Label', 'qode-variation-swatches-for-woocommerce' ),
							)
						);
					}

					// Hook to include additional options after module options.
					do_action( 'qode_variation_swatches_for_woocommerce_action_after_attribute_terms_options_map', $page, $tax );
				}
			}
		}
	}

	add_action( 'qode_variation_swatches_for_woocommerce_action_framework_custom_taxonomy_fields', 'qode_variation_swatches_for_woocommerce_add_product_attribute_terms_options' );
}
