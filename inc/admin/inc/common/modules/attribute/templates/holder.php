<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

foreach ( $this_object->get_child_elements() as $key => $child ) {
	foreach ( $child->get_scope() as $scope ) {
		if ( $type === $scope ) {
			$child->set_layout( $layout );
			$child->render();

			wp_nonce_field(
				'qode_variation_swatches_for_woocommerce_framework_attribute_nonce',
				'qode_variation_swatches_for_woocommerce_framework_attribute_nonce'
			);
		}
	}
}
