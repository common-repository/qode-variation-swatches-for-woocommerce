<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! class_exists( 'Qode_Variation_Swatches_For_WooCommerce_Frontend_Module' ) ) {
	class Qode_Variation_Swatches_For_WooCommerce_Frontend_Module {
		private static $instance;

		public function __construct() {

			add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'render_variations' ), 10, 2 );

			// Set inline styles.
			add_filter( 'qode_variation_swatches_for_woocommerce_filter_add_inline_style', array( $this, 'set_inline_styles' ) );

			$this->include_woocommerce_variation_swatches_scripts();

			add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array( $this, 'add_additional_variation_select_classes' ) );

			add_action( 'woocommerce_before_single_product', array( $this, 'include_woocommerce_variation_swatches_product_attributes' ), 10, 2 );

			$this->add_variations_holder();
		}

		/**
		 * Instance of module class
		 *
		 * @return Qode_Variation_Swatches_For_WooCommerce_Frontend_Module
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function include_woocommerce_variation_swatches_scripts() {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
		}

		public function include_woocommerce_variation_swatches_product_attributes() {
			$custom_attributes_variations = $this->create_custom_attributes_array( qode_variation_swatches_for_woocommerce_get_global_product() );

			wp_localize_script( 'wc-add-to-cart-variation', 'qodeVariationSwatchesForWooCommerceCustomProductAttributes', array( 'attributes' => wp_json_encode( $custom_attributes_variations ) ) );
		}

		public function create_custom_attributes_array( $product, $attributes_to_check = array() ) {
			global $wc_product_attributes;

			$attributes        = $product->get_attributes();
			$custom_attr_types = qode_variation_swatches_for_woocommerce_get_attribute_types();

			$single_product_attributes = array();

			if ( ! is_array( $attributes ) ) {
				return;
			}

			foreach ( $attributes as $attribute ) {

				// check if current attribute is used for variations otherwise continue.
				if ( ! isset( $attribute['name'] ) || ( ! empty( $attributes_to_check ) && ! array_key_exists( $attribute['name'], $attributes_to_check ) ) ) {
					continue;
				}

				if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
					$taxonomy_name = wc_sanitize_taxonomy_name( $attribute['name'] );

					if ( ! taxonomy_exists( $taxonomy_name ) ) {
						continue;
					}

					// get taxonomy.
					$attribute_taxonomy = $wc_product_attributes[ $taxonomy_name ];
				} else {
					$taxonomy_name      = $attribute['name'];
					$attribute_taxonomy = (object) array( 'attribute_type' => 'select' );
				}

				// if is custom add values.
				if ( array_key_exists( $attribute_taxonomy->attribute_type, $custom_attr_types ) ) {

					// add type value.
					$single_product_attributes[ $taxonomy_name ]['type'] = $attribute_taxonomy->attribute_type;

					// get terms and add to array.
					$product_id = $product->get_id();
					$terms      = wc_get_product_terms( $product_id, $taxonomy_name, array( 'fields' => 'all' ) );

					foreach ( $terms as $term ) {

						if ( property_exists( $term, 'term_id' ) ) {
							$term_id = qode_variation_swatches_for_woocommerce_get_original_term_id( $term->term_id, $taxonomy_name );
							$value   = qode_variation_swatches_for_woocommerce_framework_get_option_value( '', 'taxonomy', 'qode_variation_swatches_for_woocommerce_' . $attribute_taxonomy->attribute_type, '', $term_id );
						}

						$term_data = array(
							'value' => $value,
							'name'  => $term->name,
						);

						$single_product_attributes[ $taxonomy_name ]['terms'][ $term->slug ] = $term_data;
					}
				}
			}

			return $single_product_attributes;
		}

		public function render_variations( $html, $args ) {
			if ( qode_variation_swatches_for_woocommerce_is_installed( 'variation-swatches-premium' ) ) {
				return $html;
			}

			$product_id = $args['product']->get_id();
			$product    = wc_get_product( $product_id );

			$attributes = $this->create_custom_attributes_array( $product );

			foreach ( $attributes as $attribute_key => $attribute ) {
				$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_key );

				$attribute['id']       = $attribute_id;
				$attribute['taxonomy'] = $attribute_key;

				if ( $attribute_key === $args['attribute'] && array_key_exists( 'type', $attribute ) ) {
					$options = $args['options'];

					// Get selected value.
					if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
						$selected_key = 'attribute_' . sanitize_title( $args['attribute'] );
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] );
					}

					$attribute_temp = wc_get_attribute( $attribute_id );
					$attribute_name = ! empty( $attribute_temp ) ? $attribute_temp->name : '';

					$select_options_container_classes = $this->get_select_options_container_classes( $attribute );

					if ( array_key_exists( 'terms', $attribute ) && 'select' !== $attribute['type'] ) {

						$html .= '<div ' . qode_variation_swatches_for_woocommerce_get_class_attribute( $select_options_container_classes ) . ' data-attribute-name="' . esc_attr( $attribute_name ) . '">';

						$filtered_array = array_intersect_key( $attribute['terms'], array_flip( $options ) );

						foreach ( $filtered_array as $key => $val ) {

							// get term name.
							$term = get_term_by( 'slug', $key, $attribute_key );
							$name = $term->name;

							$attribute_term = array(
								'key'          => $key,
								'value'        => $val['value'],
								'name'         => $name,
								'type'         => $attribute['type'],
								'selected'     => false,
								'attribute_id' => $attribute_id,
							);

							if ( $key === $args['selected'] ) {
								$attribute_term['selected'] = true;
							}

							$attribute_term['styles']               = $this->get_attribute_styles( $attribute_term );
							$attribute_term['variation_classes']    = $this->get_attribute_classes( $attribute_term );
							$attribute_term['attribute_term_attrs'] = $this->get_attribute_term_attrs( $attribute_term );

							$html .= qode_variation_swatches_for_woocommerce_get_template_part( 'general', 'templates/' . $attribute_term['type'], '', $attribute_term );
						}

						$html .= '</div>';
					}
				}
			}

			return $html;
		}

		private function get_attribute_styles( $atts ) {
			$styles = array();

			if ( 'color' === $atts['type'] && ! empty( $atts['value'] ) ) {
				$styles[] = 'background-color: ' . $atts['value'];
			}

			return $styles;
		}

		private function get_attribute_classes( $atts ) {
			$variation_classes = array();

			$variation_classes[] = 'qvsfw-select-option';
			$variation_classes[] = 'qvsfw-select-option--' . $atts['type'];
			$variation_classes[] = ! empty( $atts['selected'] ) && true === $atts['selected'] ? 'qvsfw-selected' : '';

			return implode( ' ', $variation_classes );
		}

		private function get_attribute_term_attrs( $atts ) {
			$attrs = array();

			if ( ! empty( $atts['key'] ) ) {
				$attrs['data-value'] = esc_attr( $atts['key'] );
			}

			if ( ! empty( $atts['name'] ) ) {
				$attrs['data-name'] = esc_attr( $atts['name'] );
			}

			return $attrs;
		}

		private function get_select_options_container_classes( $attribute ) {
			$select_options_container_classes = array();

			$select_options_container_classes[] = 'qvsfw-select-options-container';
			$select_options_container_classes[] = 'qvsfw-select-options-container-type--' . $attribute['type'];
			$select_options_container_classes[] = 'qvsfw-disabled-attribute--crossed-out';

			if ( ! qode_variation_swatches_for_woocommerce_is_installed( 'variation-swatches-premium' ) ) {
				$select_options_container_classes[] = 'qvsfw-style-layout--simple';
			}

			$select_options_container_classes[] = $attribute['taxonomy'];

			return implode( ' ', $select_options_container_classes );
		}

		public function set_inline_styles( $style ) {
			if ( qode_variation_swatches_for_woocommerce_is_installed( 'variation-swatches-premium' ) ) {
				return $style;
			}

			$width                 = qode_variation_swatches_for_woocommerce_get_option_value( 'admin', 'qode_variation_swatches_for_woocommerce_variation_swatches_min_width' );
			$height                = qode_variation_swatches_for_woocommerce_get_option_value( 'admin', 'qode_variation_swatches_for_woocommerce_variation_swatches_height' );
			$border_color          = qode_variation_swatches_for_woocommerce_get_option_value( 'admin', 'qode_variation_swatches_for_woocommerce_variation_swatches_border_color' );
			$selected_border_color = qode_variation_swatches_for_woocommerce_get_option_value( 'admin', 'qode_variation_swatches_for_woocommerce_variation_swatches_selected_border_color' );
			$space_between         = qode_variation_swatches_for_woocommerce_get_option_value( 'admin', 'qode_variation_swatches_for_woocommerce_space_between_variation_swatches' );

			$styles = array();

			$attribute_types = array( 'color', 'image', 'label' );
			foreach ( $attribute_types as $type ) {

				if ( ! empty( $width ) ) {
					if ( qode_variation_swatches_for_woocommerce_string_ends_with_typography_units( $width ) ) {
						$styles[ '--qvsfw-variation-' . $type . '-option-min-width' ] = $width;
					} else {
						$styles[ '--qvsfw-variation-' . $type . '-option-min-width' ] = intval( $width ) . 'px';
					}
				}

				if ( ! empty( $height ) ) {
					if ( qode_variation_swatches_for_woocommerce_string_ends_with_typography_units( $height ) ) {
						$styles[ '--qvsfw-variation-' . $type . '-option-height' ] = $height;
					} else {
						$styles[ '--qvsfw-variation-' . $type . '-option-height' ] = intval( $height ) . 'px';
					}
				}

				if ( ! empty( $border_color ) ) {
					$styles[ '--qvsfw-variation-' . $type . '-option-border-color' ] = $border_color;
				}

				if ( ! empty( $selected_border_color ) ) {
					$styles[ '--qvsfw-variation-' . $type . '-option-selected-border-color' ] = $selected_border_color;
				}
			}

			if ( ! empty( $border_color ) ) {
				$styles['--qvsfw-variation-common-border-color'] = $border_color;
			}

			if ( ! empty( $selected_border_color ) ) {
				$styles['--qvsfw-variation-common-selected-border-color'] = $selected_border_color;
			}

			if ( '' !== $space_between ) {
				$styles['--qvsfw-variation-space-between-options'] = intval( $space_between ) . 'px';
			}

			if ( ! empty( $styles ) ) {
				$style .= qode_variation_swatches_for_woocommerce_dynamic_style( ':root', $styles );
			}

			return $style;
		}

		public function add_additional_variation_select_classes( $args ) {
			global $wc_product_attributes;

			$dropdowns_to_label = qode_variation_swatches_for_woocommerce_get_option_value( 'admin', 'qode_variation_swatches_for_woocommerce_dropdowns_to_label' );

			if ( isset( $wc_product_attributes[ $args['attribute'] ] ) ) {
				$attribute_taxonomy = $wc_product_attributes[ $args['attribute'] ];
				$attribute_type     = $attribute_taxonomy->attribute_type;

				if ( ! empty( $attribute_type ) ) {
					// Modify the $args array as needed.
					$args['class'] = 'qvsfw-attribute-type--' . $attribute_type;

					if ( 'select' === $attribute_type && 'yes' === $dropdowns_to_label ) {
						$args['class'] = 'qvsfw-attribute-type--label';
					}
				}
			} else {
				$args['class'] = 'qvsfw-attribute-type--select';

				if ( 'yes' === $dropdowns_to_label ) {
					$args['class'] = 'qvsfw-attribute-type--label';
				}
			}

			return $args;
		}

		public function add_variations_holder() {
			add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'render_content_before_add_to_cart_form' ) );
			add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'render_content_after_add_to_cart_form' ) );
		}

		public function render_content_before_add_to_cart_form() {
			$product = qode_variation_swatches_for_woocommerce_get_global_product();

			if ( ! empty( $product ) && $product->is_type( 'variable' ) ) {
				echo '<div id="qvsfw-variations-form-wrapper">';
			}
		}

		public function render_content_after_add_to_cart_form() {
			$product = qode_variation_swatches_for_woocommerce_get_global_product();

			if ( ! empty( $product ) && $product->is_type( 'variable' ) ) {
				echo '</div>';
			}
		}
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_init_frontend_module' ) ) {
	/**
	 * Init main color and label variations backend module instance.
	 */
	function qode_variation_swatches_for_woocommerce_init_frontend_module() {
		Qode_Variation_Swatches_For_WooCommerce_Frontend_Module::get_instance();
	}

	// Permission 15 is set in order to load after option initialization ( init_options method).
	add_action( 'init', 'qode_variation_swatches_for_woocommerce_init_frontend_module', 15 );
}
