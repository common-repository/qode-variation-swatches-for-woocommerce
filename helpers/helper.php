<?php

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_is_installed' ) ) {
	/**
	 * Function check is some plugin is installed
	 *
	 * @param string $plugin name
	 *
	 * @return bool
	 */
	function qode_variation_swatches_for_woocommerce_is_installed( $plugin ) {
		switch ( $plugin ) :
			case 'variation-swatches-premium':
				return defined( 'QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_PREMIUM_VERSION' );
			case 'wishlist-premium':
				return defined( 'QODE_WISHLIST_FOR_WOOCOMMERCE_PREMIUM_VERSION' );
			case 'quick-view':
				return defined( 'QODE_QUICK_VIEW_FOR_WOOCOMMERCE_VERSION' );
			case 'woocommerce':
				return class_exists( 'WooCommerce' );
			case 'wpml':
				return defined( 'ICL_SITEPRESS_VERSION' );
			default:
				return apply_filters( 'qode_variation_swatches_for_woocommerce_filter_is_plugin_installed', false, $plugin );

		endswitch;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_sanitize_module_template_part' ) ) {
	/**
	 * Sanitize module template part.
	 *
	 * @param string $template temp path to file that is being loaded
	 *
	 * @return string - string with template path
	 */
	function qode_variation_swatches_for_woocommerce_sanitize_module_template_part( $template ) {
		$available_characters = '/[^A-Za-z0-9\_\-\/]/';

		if ( ! empty( $template ) && is_scalar( $template ) ) {
			$template = preg_replace( $available_characters, '', $template );
		} else {
			$template = '';
		}

		return $template;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_template_with_slug' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $temp temp path to file that is being loaded
	 * @param string $slug slug that should be checked if exists
	 *
	 * @return string - string with template path
	 */
	function qode_variation_swatches_for_woocommerce_get_template_with_slug( $temp, $slug ) {
		$template = '';

		if ( ! empty( $temp ) ) {
			$slug = qode_variation_swatches_for_woocommerce_sanitize_module_template_part( $slug );

			if ( ! empty( $slug ) ) {
				$template = "$temp-$slug.php";

				if ( ! file_exists( $template ) ) {
					$template = $temp . '.php';
				}
			} else {
				$template = $temp . '.php';
			}
		}

		return $template;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_template_part' ) ) {
	/**
	 * Loads module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 *
	 * @return string - string containing html of template
	 */
	function qode_variation_swatches_for_woocommerce_get_template_part( $module, $template, $slug = '', $params = array() ) {
		$module   = qode_variation_swatches_for_woocommerce_sanitize_module_template_part( $module );
		$template = qode_variation_swatches_for_woocommerce_sanitize_module_template_part( $template );

		$temp = QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_INC_PATH . '/' . $module . '/' . $template;

		$template = qode_variation_swatches_for_woocommerce_get_template_with_slug( $temp, $slug );

		if ( ! empty( $template ) && file_exists( $template ) ) {
			// Extract params so they could be used in template.
			if ( is_array( $params ) && count( $params ) ) {
				// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
				extract( $params, EXTR_SKIP ); // @codingStandardsIgnoreLine
			}

			ob_start();

			// nosemgrep audit.php.lang.security.file.inclusion-arg.
			include qode_variation_swatches_for_woocommerce_get_template_with_slug( $temp, $slug );

			$html = ob_get_clean();

			return $html;
		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_template_part' ) ) {
	/**
	 * Echo module template part.
	 *
	 * @param string $module name of the module from inc folder
	 * @param string $template full path of the template to load
	 * @param string $slug
	 * @param array $params array of parameters to pass to template
	 */
	function qode_variation_swatches_for_woocommerce_template_part( $module, $template, $slug = '', $params = array() ) {
		$module_template_part = qode_variation_swatches_for_woocommerce_get_template_part( $module, $template, $slug, $params );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_variation_swatches_for_woocommerce_framework_wp_kses_html( 'html', $module_template_part );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_option_value' ) ) {
	/**
	 * Function that returns option value using framework function but providing it's own scope
	 *
	 * @param string $type option type
	 * @param string $name name of option
	 * @param string $default_value option default value
	 * @param int $post_id id of
	 *
	 * @return string value of option
	 */
	function qode_variation_swatches_for_woocommerce_get_option_value( $type, $name, $default_value = '', $post_id = null ) {
		$scope = QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_OPTIONS_NAME;

		return qode_variation_swatches_for_woocommerce_framework_get_option_value( $scope, $type, $name, $default_value, $post_id );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_post_value_through_levels' ) ) {
	/**
	 * Function that returns meta value if exists, otherwise global value using framework function but providing it's own scope
	 *
	 * @param string $name name of option
	 * @param int $post_id id of
	 *
	 * @return string|array value of option
	 */
	function qode_variation_swatches_for_woocommerce_get_post_value_through_levels( $name, $post_id = null ) {
		$scope = QODE_VARIATION_SWATCHES_FOR_WOOCOMMERCE_OPTIONS_NAME;

		return qode_variation_swatches_for_woocommerce_framework_get_post_value_through_levels( $scope, $name, $post_id );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_render_svg_icon' ) ) {
	/**
	 * Function that print svg html icon
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 */
	function qode_variation_swatches_for_woocommerce_render_svg_icon( $name, $class_name = '' ) {
		$svg_template_part = qode_variation_swatches_for_woocommerce_get_svg_icon( $name, $class_name );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_variation_swatches_for_woocommerce_framework_wp_kses_html( 'html', $svg_template_part );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_svg_icon' ) ) {
	/**
	 * Returns svg html
	 *
	 * @param string $name - icon name
	 * @param string $class_name - custom html tag class name
	 *
	 * @return string
	 */
	function qode_variation_swatches_for_woocommerce_get_svg_icon( $name, $class_name = '' ) {
		$html  = '';
		$class = isset( $class_name ) && ! empty( $class_name ) ? esc_attr( $class_name ) : '';

		switch ( $name ) {
			case 'expand':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="92px" height="92px" viewBox="0 0 92 92" enable-background="new 0 0 92 92" xml:space="preserve"><path d="M90,6l0,20c0,2.2-1.8,4-4,4l0,0c-2.2,0-4-1.8-4-4V15.7L58.8,38.9c-0.8,0.8-1.8,1.2-2.8,1.2c-1,0-2-0.4-2.8-1.2c-1.6-1.6-1.6-4.1,0-5.7L76.3,10H66c-2.2,0-4-1.8-4-4c0-2.2,1.8-4,4-4h20c1.1,0,2.1,0.4,2.8,1.2C89.6,3.9,90,4.9,90,6z M86,62c-2.2,0-4,1.8-4,4v10.3L59.2,53.7c-1.6-1.6-4.2-1.6-5.8,0c-1.6,1.6-1.6,4.1-0.1,5.7L75.9,82H65.6c0,0,0,0,0,0c-2.2,0-4,1.8-4,4s1.8,4,4,4l20,0l0,0c1.1,0,2.3-0.4,3-1.2c0.8-0.8,1.4-1.8,1.4-2.8V66C90,63.8,88.2,62,86,62zM32.8,53.5L10,76.3V66c0-2.2-1.8-4-4-4h0c-2.2,0-4,1.8-4,4l0,20c0,1.1,0.4,2.1,1.2,2.8C4,89.6,5,90,6.1,90h20c2.2,0,4-1.8,4-4c0-2.2-1.8-4-4-4H15.7l22.8-22.8c1.6-1.6,1.5-4.1,0-5.7C37,51.9,34.4,51.9,32.8,53.5z M15.7,10.4l10.3,0h0c2.2,0,4-1.8,4-4s-1.8-4-4-4l-20,0h0c-1.1,0-2.1,0.4-2.8,1.2C2.4,4.3,2,5.3,2,6.4l0,20c0,2.2,1.8,4,4,4c2.2,0,4-1.8,4-4V16l23.1,23.1c0.8,0.8,1.8,1.2,2.8,1.2c1,0,2-0.4,2.8-1.2c1.6-1.6,1.6-4.1,0-5.7L15.7,10.4z"/></svg>';
				break;
			case 'trash':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="92px" height="92px" viewBox="0 0 92 92" enable-background="new 0 0 92 92" xml:space="preserve"><path d="M78.4,30.4l-3.1,57.8c-0.1,2.1-1.9,3.8-4,3.8H20.7c-2.1,0-3.9-1.7-4-3.8l-3.1-57.8c-0.1-2.2,1.6-4.1,3.8-4.2c2.2-0.1,4.1,1.6,4.2,3.8l2.9,54h43.1l2.9-54c0.1-2.2,2-3.9,4.2-3.8C76.8,26.3,78.5,28.2,78.4,30.4zM89,17c0,2.2-1.8,4-4,4H7c-2.2,0-4-1.8-4-4s1.8-4,4-4h22V4c0-1.9,1.3-3,3.2-3h27.6C61.7,1,63,2.1,63,4v9h22C87.2,13,89,14.8,89,17zM36,13h20V8H36V13z M37.7,78C37.7,78,37.7,78,37.7,78c2,0,3.5-1.9,3.5-3.8l-1-43.2c0-1.9-1.6-3.5-3.6-3.5c-1.9,0-3.5,1.6-3.4,3.6l1,43.3C34.2,76.3,35.8,78,37.7,78z M54.2,78c1.9,0,3.5-1.6,3.5-3.5l1-43.2c0-1.9-1.5-3.6-3.4-3.6c-2,0-3.5,1.5-3.6,3.4l-1,43.2C50.6,76.3,52.2,78,54.2,78C54.1,78,54.1,78,54.2,78z"/></svg>';
				break;
			case 'search':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M18.869 19.162l-5.943-6.484c1.339-1.401 2.075-3.233 2.075-5.178 0-2.003-0.78-3.887-2.197-5.303s-3.3-2.197-5.303-2.197-3.887 0.78-5.303 2.197-2.197 3.3-2.197 5.303 0.78 3.887 2.197 5.303 3.3 2.197 5.303 2.197c1.726 0 3.362-0.579 4.688-1.645l5.943 6.483c0.099 0.108 0.233 0.162 0.369 0.162 0.121 0 0.242-0.043 0.338-0.131 0.204-0.187 0.217-0.503 0.031-0.706zM1 7.5c0-3.584 2.916-6.5 6.5-6.5s6.5 2.916 6.5 6.5-2.916 6.5-6.5 6.5-6.5-2.916-6.5-6.5z"></path></svg>';
				break;
			case 'spinner':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z"></path></svg>';
				break;
			case 'slider-arrow-left':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" width="5.1982" height="8.0957" viewBox="0 0 5.1982 8.0957"><path d="M4.0454,0a.3117.3117,0,0,1,.2285.0952l.8286.834a.3143.3143,0,0,1,0,.4517l-2.667,2.667,2.667,2.667a.3143.3143,0,0,1,0,.4517L4.2739,8a.3218.3218,0,0,1-.457,0L.0952,4.2739a.316.316,0,0,1,0-.4521L3.8169.0952A.3117.3117,0,0,1,4.0454,0Z"/></svg>';
				break;
			case 'slider-arrow-right':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" width="5.1982" height="8.0957" viewBox="0 0 5.1982 8.0957"><path d="M1.3813.0952,5.103,3.8218a.316.316,0,0,1,0,.4521L1.3813,8a.3218.3218,0,0,1-.457,0l-.8286-.834a.3143.3143,0,0,1,0-.4517l2.667-2.667L.0957,1.3809a.3143.3143,0,0,1,0-.4517L.9243.0952a.3218.3218,0,0,1,.457,0Z"/></svg>';
				break;
			case 'info':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1536 1536"><path fill="currentColor" d="M1024 1248v-160q0-14-9-23t-23-9h-96V544q0-14-9-23t-23-9H544q-14 0-23 9t-9 23v160q0 14 9 23t23 9h96v320h-96q-14 0-23 9t-9 23v160q0 14 9 23t23 9h448q14 0 23-9t9-23zM896 352V192q0-14-9-23t-23-9H672q-14 0-23 9t-9 23v160q0 14 9 23t23 9h192q14 0 23-9t9-23zm640 416q0 209-103 385.5T1153.5 1433T768 1536t-385.5-103T103 1153.5T0 768t103-385.5T382.5 103T768 0t385.5 103T1433 382.5T1536 768z"/></svg>';
				break;
			case 'close':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" width="18.1213" height="18.1213" viewBox="0 0 18.1213 18.1213"><line x1="1.0607" y1="1.0607" x2="17.0607" y2="17.0607"/><line x1="17.0607" y1="1.0607" x2="1.0607" y2="17.0607"/></svg>';
				break;
			case 'notify':
				$html = '<svg class="' . esc_attr( $class ) . '" xmlns="http://www.w3.org/2000/svg" width="10.2139" height="11" viewBox="0 0 10.2139 11"><path d="M10.2139,8.6426a.7963.7963,0,0,1-.7856.7861h-2.75A1.5712,1.5712,0,0,1,5.1069,11,1.5712,1.5712,0,0,1,3.5356,9.4287H.7856a.757.757,0,0,1-.5527-.2334A.7557.7557,0,0,1,0,8.6426a5.3877,5.3877,0,0,0,.5586-.54A4.8583,4.8583,0,0,0,1.08,7.3691a5.5234,5.5234,0,0,0,.4575-.9727,7.172,7.172,0,0,0,.3066-1.2646,10.0089,10.0089,0,0,0,.12-1.5957,2.5545,2.5545,0,0,1,.7183-1.7344A3.0328,3.0328,0,0,1,4.5669.8291.6156.6156,0,0,1,4.5176.59a.5694.5694,0,0,1,.1719-.418.593.593,0,0,1,.835,0A.5713.5713,0,0,1,5.6963.59.6156.6156,0,0,1,5.647.8291a3.0328,3.0328,0,0,1,1.8848.9727A2.5545,2.5545,0,0,1,8.25,3.5361a10.0089,10.0089,0,0,0,.12,1.5957,7.172,7.172,0,0,0,.3066,1.2646,5.5234,5.5234,0,0,0,.4575.9727,4.8583,4.8583,0,0,0,.5215.7334A5.3877,5.3877,0,0,0,10.2139,8.6426ZM5.2051,10.4111a.0871.0871,0,0,0-.0981-.0986.89.89,0,0,1-.8838-.8838.0984.0984,0,1,0-.1968,0,1.0786,1.0786,0,0,0,1.0806,1.08A.0864.0864,0,0,0,5.2051,10.4111Z"/></svg>';
				break;
		}

		return apply_filters( 'qode_variation_swatches_for_woocommerce_filter_svg_icon', $html, $name, $class_name );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_class_attribute' ) ) {
	/**
	 * Function that echoes class attribute
	 *
	 * @param string|array $value - value of class attribute
	 *
	 * @see qode_variation_swatches_for_woocommerce_get_class_attribute()
	 */
	function qode_variation_swatches_for_woocommerce_class_attribute( $value ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post( qode_variation_swatches_for_woocommerce_get_class_attribute( $value ) );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_class_attribute' ) ) {
	/**
	 * Function that returns generated class attribute
	 *
	 * @param string|array $value - value of class attribute
	 *
	 * @return string generated class attribute
	 *
	 * @see qode_variation_swatches_for_woocommerce_get_inline_attr()
	 */
	function qode_variation_swatches_for_woocommerce_get_class_attribute( $value ) {
		return qode_variation_swatches_for_woocommerce_get_inline_attr( $value, 'class', ' ' );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_id_attribute' ) ) {
	/**
	 * Function that echoes id attribute
	 *
	 * @param string|array $value - value of id attribute
	 *
	 * @see qode_variation_swatches_for_woocommerce_get_id_attribute()
	 */
	function qode_variation_swatches_for_woocommerce_id_attribute( $value ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wp_kses_post( qode_variation_swatches_for_woocommerce_get_id_attribute( $value ) );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_id_attribute' ) ) {
	/**
	 * Function that returns generated id attribute
	 *
	 * @param string|array $value - value of id attribute
	 *
	 * @return string generated id attribute
	 *
	 * @see qode_variation_swatches_for_woocommerce_get_inline_attr()
	 */
	function qode_variation_swatches_for_woocommerce_get_id_attribute( $value ) {
		return qode_variation_swatches_for_woocommerce_get_inline_attr( $value, 'id', ' ' );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_inline_style' ) ) {
	/**
	 * Function that echoes generated style attribute
	 *
	 * @param string|array $value - attribute value
	 *
	 * @see qode_variation_swatches_for_woocommerce_get_inline_style()
	 */
	function qode_variation_swatches_for_woocommerce_inline_style( $value ) {
		$inline_style_part = qode_variation_swatches_for_woocommerce_get_inline_style( $value );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_variation_swatches_for_woocommerce_framework_wp_kses_html( 'attributes', $inline_style_part );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_inline_style' ) ) {
	/**
	 * Function that generates style attribute and returns generated string
	 *
	 * @param string|array $value - value of style attribute
	 *
	 * @return string generated style attribute
	 *
	 * @see qode_variation_swatches_for_woocommerce_get_inline_style()
	 */
	function qode_variation_swatches_for_woocommerce_get_inline_style( $value ) {
		return qode_variation_swatches_for_woocommerce_get_inline_attr( $value, 'style', ';' );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_inline_attrs' ) ) {
	/**
	 * Echo multiple inline attributes
	 *
	 * @param array $attrs
	 * @param bool $allow_zero_values
	 */
	function qode_variation_swatches_for_woocommerce_inline_attrs( $attrs, $allow_zero_values = false ) {
		$inline_attrs_part = qode_variation_swatches_for_woocommerce_get_inline_attrs( $attrs, $allow_zero_values );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo qode_variation_swatches_for_woocommerce_framework_wp_kses_html( 'attributes', $inline_attrs_part );
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_inline_attrs' ) ) {
	/**
	 * Generate multiple inline attributes
	 *
	 * @param array $attrs
	 * @param bool $allow_zero_values
	 *
	 * @return string
	 */
	function qode_variation_swatches_for_woocommerce_get_inline_attrs( $attrs, $allow_zero_values = false ) {
		$output = '';
		if ( is_array( $attrs ) && count( $attrs ) ) {
			if ( $allow_zero_values ) {
				foreach ( $attrs as $attr => $value ) {
					$output .= ' ' . qode_variation_swatches_for_woocommerce_get_inline_attr( $value, $attr, '', true );
				}
			} else {
				foreach ( $attrs as $attr => $value ) {
					$output .= ' ' . qode_variation_swatches_for_woocommerce_get_inline_attr( $value, $attr );
				}
			}
		}

		$output = ltrim( $output );

		return $output;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_inline_attr' ) ) {
	/**
	 * Function that generates html attribute
	 *
	 * @param string|array $value value of html attribute
	 * @param string $attr - name of html attribute to generate
	 * @param string $glue - glue with which to implode $attr. Used only when $attr is arrayed
	 * @param bool $allow_zero_values - allow data to have zero value
	 *
	 * @return string generated html attribute
	 */
	function qode_variation_swatches_for_woocommerce_get_inline_attr( $value, $attr, $glue = '', $allow_zero_values = false ) {
		if ( $allow_zero_values ) {
			if ( '' !== $value ) {

				if ( is_array( $value ) && count( $value ) ) {
					$properties = implode( $glue, $value );
				} else {
					$properties = $value;
				}

				return $attr . '="' . esc_attr( $properties ) . '"';
			}
		} else {
			if ( ! empty( $value ) ) {

				if ( is_array( $value ) && count( $value ) ) {
					$properties = implode( $glue, $value );
				} elseif ( '' !== $value ) {
					$properties = $value;
				} else {
					return '';
				}

				return $attr . '="' . esc_attr( $properties ) . '"';
			}
		}

		return '';
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_string_ends_with' ) ) {
	/**
	 * Checks if $haystack ends with $needle and returns proper bool value
	 *
	 * @param string $haystack - to check
	 * @param string $needle - on end to match
	 *
	 * @return bool
	 */
	function qode_variation_swatches_for_woocommerce_string_ends_with( $haystack, $needle ) {
		if ( '' !== $haystack && '' !== $needle ) {
			return ( substr( $haystack, - strlen( $needle ), strlen( $needle ) ) === $needle );
		}

		return false;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_string_ends_with_typography_units' ) ) {
	/**
	 * Checks if $haystack ends with predefined needles and returns proper bool value
	 *
	 * @param string $haystack - to check
	 *
	 * @return bool
	 */
	function qode_variation_swatches_for_woocommerce_string_ends_with_typography_units( $haystack ) {
		$result  = false;
		$needles = array( 'px', 'em', 'rem', 'vh', 'vw', '%' );

		if ( '' !== $haystack ) {
			foreach ( $needles as $needle ) {
				if ( qode_variation_swatches_for_woocommerce_string_ends_with( $haystack, $needle ) ) {
					$result = true;
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_dynamic_style' ) ) {
	/**
	 * Outputs css based on passed selectors and properties
	 *
	 * @param array|string $selector
	 * @param array $properties
	 *
	 * @return string
	 */
	function qode_variation_swatches_for_woocommerce_dynamic_style( $selector, $properties ) {
		$output = '';
		// check if selector and rules are valid data.
		if ( ! empty( $selector ) && ( is_array( $properties ) && count( $properties ) ) ) {

			if ( is_array( $selector ) && count( $selector ) ) {
				$output .= implode( ', ', $selector );
			} else {
				$output .= $selector;
			}

			$output .= ' { ';
			foreach ( $properties as $prop => $value ) {
				if ( '' !== $prop ) {

					if ( 'font-family' === $prop ) {
						$output .= $prop . ': "' . esc_attr( $value ) . '";';
					} else {
						$output .= $prop . ': ' . esc_attr( $value ) . ';';
					}
				}
			}

			$output .= '}';
		}

		return $output;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_dynamic_style_responsive' ) ) {
	/**
	 * Outputs css based on passed selectors and properties
	 *
	 * @param array|string $selector
	 * @param array $properties
	 * @param string $min_width
	 * @param string $max_width
	 *
	 * @return string
	 */
	function qode_variation_swatches_for_woocommerce_dynamic_style_responsive( $selector, $properties, $min_width = '', $max_width = '' ) {
		$output = '';
		// check if min width or max width is set.
		if ( ! empty( $min_width ) || ! empty( $max_width ) ) {
			$output .= '@media only screen';

			if ( ! empty( $min_width ) ) {
				$output .= ' and (min-width: ' . $min_width . 'px)';
			}

			if ( ! empty( $max_width ) ) {
				$output .= ' and (max-width: ' . $max_width . 'px)';
			}

			$output .= ' { ';

			$output .= qode_variation_swatches_for_woocommerce_dynamic_style( $selector, $properties );

			$output .= '}';
		}

		return $output;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_attachment_id_from_url' ) ) {
	/**
	 * Function that retrieves attachment id for passed attachment url
	 *
	 * @param string $attachment_url
	 *
	 * @return null|string
	 */
	function qode_variation_swatches_for_woocommerce_get_attachment_id_from_url( $attachment_url ) {
		global $wpdb;
		$attachment_id = '';

		if ( '' !== $attachment_url ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid=%s", $attachment_url ) );

			// Additional check for undefined reason when guid is not image src.
			if ( empty( $attachment_id ) ) {
				$modified_url = substr( $attachment_url, strrpos( $attachment_url, '/' ) + 1 );

				// Get attachment id.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file' AND meta_value LIKE %s", '%' . $modified_url . '%' ) );
			}
		}

		return $attachment_id;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_ajax_status' ) ) {
	/**
	 * Function that return status from ajax functions
	 *
	 * @param string $status - success or error
	 * @param string $message - ajax message value
	 * @param string|array $data - returned value
	 * @param string $redirect - url address
	 */
	function qode_variation_swatches_for_woocommerce_get_ajax_status( $status, $message, $data = null, $redirect = '' ) {
		$response = array(
			'status'   => esc_attr( $status ),
			'message'  => esc_html( $message ),
			'data'     => $data,
			'redirect' => ! empty( $redirect ) ? esc_url( $redirect ) : '',
		);

		$output = wp_json_encode( $response );

		exit( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_cpt_items' ) ) {
	/**
	 * Returns array of custom post items
	 *
	 * @param string $cpt_slug
	 * @param array $args
	 * @param bool $enable_default - add first element empty for default value
	 *
	 * @return array
	 */
	function qode_variation_swatches_for_woocommerce_get_cpt_items( $cpt_slug = 'product', $args = array(), $enable_default = true ) {
		$options    = array();
		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => $cpt_slug,
			'posts_per_page' => '-1',
			'fields'         => 'ids',
		);

		if ( ! empty( $args ) ) {
			foreach ( $args as $key => $value ) {
				if ( ! empty( $value ) ) {
					$query_args[ $key ] = $value;
				}
			}
		}

		$cpt_items = new \WP_Query( $query_args );

		if ( $cpt_items->have_posts() ) {

			if ( $enable_default ) {
				$options[''] = esc_html__( 'Default', 'qode-variation-swatches-for-woocommerce' );
			}

			foreach ( $cpt_items->posts as $id ) :
				$options[ $id ] = get_the_title( $id );
			endforeach;
		}

		wp_reset_postdata();

		return $options;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_get_global_product' ) ) {
	/**
	 * Function that return global WooCommerce object
	 *
	 * @return object
	 */
	function qode_variation_swatches_for_woocommerce_get_global_product() {
		global $product;

		return $product;
	}
}

if ( ! function_exists( 'qode_variation_swatches_for_woocommerce_is_woo_page' ) ) {
	/**
	 * Function that check WooCommerce pages
	 *
	 * @param string $page
	 *
	 * @return bool
	 */
	function qode_variation_swatches_for_woocommerce_is_woo_page( $page ) {
		switch ( $page ) {
			case 'shop':
				return function_exists( 'is_shop' ) && is_shop();
			case 'single':
				return is_singular( 'product' );
			case 'cart':
				return function_exists( 'is_cart' ) && is_cart();
			case 'checkout':
				return function_exists( 'is_checkout' ) && is_checkout();
			case 'account':
				return function_exists( 'is_account_page' ) && is_account_page();
			case 'category':
				return function_exists( 'is_product_category' ) && is_product_category();
			case 'tag':
				return function_exists( 'is_product_tag' ) && is_product_tag();
			case 'any':
				return ( function_exists( 'is_shop' ) && is_shop() ) ||
					is_singular( 'product' ) ||
					( function_exists( 'is_cart' ) && is_cart() ) ||
					( function_exists( 'is_checkout' ) && is_checkout() ) ||
					( function_exists( 'is_account_page' ) && is_account_page() ) ||
					( function_exists( 'is_product_category' ) && is_product_category() ) ||
					( function_exists( 'is_product_tag' ) && is_product_tag() );
			case 'archive':
				return ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() );
			default:
				return false;
		}
	}
}
