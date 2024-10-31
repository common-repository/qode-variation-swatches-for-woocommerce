<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

class Qode_Variation_Swatches_For_WooCommerce_Framework_Field_Textareahtml extends Qode_Variation_Swatches_For_WooCommerce_Framework_Field_Type {

	public function render_field() {
		?>
		<textarea class="form-control qodef-field qodef--field-html" <?php qode_variation_swatches_for_woocommerce_inline_attrs( $this->data_attrs ); ?> name="<?php echo esc_attr( $this->name ); ?>" rows="10"
		<?php
		if ( isset( $this->args['readonly'] ) ) {
			echo ' readonly';
		}
		?>
		><?php echo wp_kses_post( $this->params['value'] ); ?></textarea>
		<?php
	}
}
