<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! empty( $value ) ) : ?>
	<span <?php qode_variation_swatches_for_woocommerce_class_attribute( $variation_classes ); ?> <?php qode_variation_swatches_for_woocommerce_inline_attrs( $attribute_term_attrs ); ?>>
		<span class="qvsfw-select-option-inner-holder">
			<span class="qvsfw-select-option-inner">
				<span class="qvsfw-select-value">
					<?php echo wp_get_attachment_image( $value, 'thumbnail' ); ?>
				</span>
			</span>
		</span>
	</span>
<?php endif; ?>
