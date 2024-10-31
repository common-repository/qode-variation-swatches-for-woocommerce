<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Variation_Swatches_For_WooCommerce_Backend_Module' ) ) {
	class Qode_Variation_Swatches_For_WooCommerce_Backend_Module {
		private static $instance;

		public $taxonomy = '';

		public function __construct() {

			// Add custom attribute types.
			add_filter( 'product_attributes_type_selector', array( $this, 'attribute_types' ) );

			// Add select field on single product page to allow custom attribute selection.
			add_action( 'woocommerce_product_option_terms', array( $this, 'display_custom_attribute_type_terms' ), 10, 2 );

			$this->extend_product_attribute_columns();
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Variation_Swatches_For_WooCommerce_Backend_Module
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function attribute_types( $default_type ) {
			$custom = qode_variation_swatches_for_woocommerce_get_attribute_types();

			return is_array( $custom ) ? array_merge( $default_type, $custom ) : $default_type;
		}

		private function get_terms( $tax_name ) {

			// Get terms.
			$terms = get_terms(
				array(
					'taxonomy'   => $tax_name,
					'orderby'    => 'name',
					'hide_empty' => false,
				)
			);

			$all_terms = array();

			foreach ( $terms as $term ) {
				$all_terms[] = array(
					'id'    => $term->term_id,
					'value' => $term->term_id,
					'name'  => $term->name,
				);
			}

			return $all_terms;
		}

		public function display_custom_attribute_type_terms( $taxonomy, $i ) {
			global $thepostid;

			if ( in_array( $taxonomy->attribute_type, array_keys( qode_variation_swatches_for_woocommerce_get_attribute_types() ), true ) ) {

				if ( 'select' !== $taxonomy->attribute_type ) {

					// Don't allow users without capabilities to modify product.
					// phpcs:ignore WordPress.WP.Capabilities.Unknown
					if ( ! current_user_can( 'edit_posts' ) ) {
						return;
					}

					$attribute_taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );

					// phpcs:ignore WordPress.Security.NonceVerification
					if ( is_null( $thepostid ) && isset( $_POST['post_id'] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification
						$thepostid = absint( $_POST['post_id'] );
					}
					?>
					<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'qode-variation-swatches-for-woocommerce' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo esc_attr( $i ); ?>][]">
						<?php
						$all_terms = $this->get_terms( $attribute_taxonomy_name );
						if ( $all_terms ) {
							foreach ( $all_terms as $term ) {
								echo '<option value="' . esc_attr( $term['value'] ) . '" ' . selected( has_term( absint( $term['id'] ), $attribute_taxonomy_name, $thepostid ), true, false ) . '>' . esc_html( $term['name'] ) . '</option>';
							}
						}
						?>
					</select>
					<button class="button plus select_all_attributes"><?php echo esc_html__( 'Select all', 'qode-variation-swatches-for-woocommerce' ); ?></button>
					<button class="button minus select_no_attributes"><?php echo esc_html__( 'Select none', 'qode-variation-swatches-for-woocommerce' ); ?></button>
					<button class="button fr plus qodef-add-new-attribute" data-attribute-type="<?php echo esc_attr( $taxonomy->attribute_type ); ?>"><?php echo esc_html__( 'Add new', 'qode-variation-swatches-for-woocommerce' ); ?></button>
					<?php
				}
			}
		}

		private function get_attribute_type( $taxonomy_name ) {
			global $wc_product_attributes;

			if ( array_key_exists( $taxonomy_name, $wc_product_attributes ) ) {
				$attribute_taxonomy = $wc_product_attributes[ $taxonomy_name ];
				$type               = $attribute_taxonomy->attribute_type;

				return $type;
			}

			return '';
		}

		public function extend_product_attribute_columns() {

			if ( isset( $_GET['taxonomy'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// phpcs:ignore WordPress.Security.NonceVerification
				$this->taxonomy = sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) );
			} elseif ( isset( $_POST['taxonomy'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				// phpcs:ignore WordPress.Security.NonceVerification
				$this->taxonomy = sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) );
			}

			if ( ! empty( $this->taxonomy ) ) {
				add_filter( 'manage_edit-' . $this->taxonomy . '_columns', array( $this, 'add_product_attribute_columns' ) );
				add_filter( 'manage_' . $this->taxonomy . '_custom_column', array( $this, 'render_attribute_custom_value' ), 10, 3 );
			}
		}

		public function add_product_attribute_columns( $columns ) {
			$attr_type = $this->get_attribute_type( $this->taxonomy );

			if ( ! in_array( $attr_type, array( 'color', 'image', 'label' ), true ) ) {
				return $columns;
			}

			$new_columns = array(
				'cb'    => isset( $columns['cb'] ) ? $columns['cb'] : null,
				'value' => esc_html__( 'Value', 'qode-variation-swatches-for-woocommerce' ),
			);

			unset( $columns['cb'] );

			return wp_parse_args( $columns, $new_columns );
		}

		public function render_attribute_custom_value( $columns, $column, $term_id ) {
			$attr_type = $this->get_attribute_type( $this->taxonomy );

			if ( ! in_array( $attr_type, array( 'color', 'image', 'label' ), true ) ) {
				return $columns;
			}

			switch ( $attr_type ) {
				case 'color':
					$color_one = qode_variation_swatches_for_woocommerce_framework_get_option_value( '', 'taxonomy', 'qode_variation_swatches_for_woocommerce_color', '', $term_id );
					$color_two = qode_variation_swatches_for_woocommerce_framework_get_option_value( '', 'taxonomy', 'qode_variation_swatches_for_woocommerce_second_color', '', $term_id );

					$styles = array();
					if ( ! empty( $color_one ) && ! empty( $color_two ) ) {
						$styles[] = 'background: linear-gradient(to bottom right, ' . $color_one . ' 50%, ' . $color_two . ' 50%';
					} elseif ( ! empty( $color_one ) ) {
						$styles[] = 'background-color: ' . $color_one;
					}
					?>

					<div class="qodef-variation-value qodef-variation-value--color" <?php qode_variation_swatches_for_woocommerce_inline_style( $styles ); ?>></div>
					<?php
					break;

				case 'image':
					$image = qode_variation_swatches_for_woocommerce_framework_get_option_value( '', 'taxonomy', 'qode_variation_swatches_for_woocommerce_image', '', $term_id );

					if ( ! empty( $image ) ) {
						$image_url = wp_get_attachment_image_url( $image, 'thumbnail' );
					} else {
						$image_url = wc_placeholder_img_src();
					}
					?>

					<img class="qodef-variation-value qodef-variation-value--image" src="<?php echo esc_url( $image_url ); ?>">
					<?php
					break;

				case 'label':
					$label = qode_variation_swatches_for_woocommerce_framework_get_option_value( '', 'taxonomy', 'qode_variation_swatches_for_woocommerce_label', '', $term_id );
					?>

					<div class="qodef-variation-value qodef-variation-value--label"><?php echo esc_html( $label ); ?></div>
					<?php
					break;
			}
		}
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_init_backend_module' ) ) {
	/**
	 * Init main color and label variations backend module instance.
	 */
	function qode_variation_swatches_for_woocommerce_init_backend_module() {
		Qode_Variation_Swatches_For_WooCommerce_Backend_Module::get_instance();
	}

	add_action( 'admin_init', 'qode_variation_swatches_for_woocommerce_init_backend_module' );
}
