<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! empty( $value ) ) : ?>
	<?php $select_option_styles[] = '--qvsfw-color-option-value: ' . $value; ?>
	<span <?php qode_variation_swatches_for_woocommerce_class_attribute( $variation_classes ); ?> <?php qode_variation_swatches_for_woocommerce_inline_attrs( $attribute_term_attrs ); ?> <?php qode_variation_swatches_for_woocommerce_inline_style( $select_option_styles ); ?>>
		<span class="qvsfw-select-option-inner">
			<span class="qvsfw-select-option-additional-holder">
				<span class="qvsfw-select-value" <?php qode_variation_swatches_for_woocommerce_inline_style( $styles ); ?>></span>
			</span>
		</span>
	</span>
<?php endif; ?>
