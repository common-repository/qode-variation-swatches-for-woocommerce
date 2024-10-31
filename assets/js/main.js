(function ( $ ) {
	'use strict';

	window.qodeVariationSwatchesForWooCommerce = {};

	qodeVariationSwatchesForWooCommerce.body         = $( 'body' );
	qodeVariationSwatchesForWooCommerce.html         = $( 'html' );
	qodeVariationSwatchesForWooCommerce.windowWidth  = $( window ).width();
	qodeVariationSwatchesForWooCommerce.windowHeight = $( window ).height();
	qodeVariationSwatchesForWooCommerce.scroll       = 0;

	$( document ).ready(
		function () {
			qodeVariationSwatchesForWooCommerce.scroll = $( window ).scrollTop();
		}
	);

	$( window ).resize(
		function () {
			qodeVariationSwatchesForWooCommerce.windowWidth  = $( window ).width();
			qodeVariationSwatchesForWooCommerce.windowHeight = $( window ).height();
		}
	);

	$( window ).scroll(
		function () {
			qodeVariationSwatchesForWooCommerce.scroll = $( window ).scrollTop();
		}
	);

	$( window ).on(
		'load',
		function () {
		}
	);

	/**
	 * Init animation on appear
	 */
	var qodeVariationSwatchesForWooCommerceAppear = {
		init: function () {
			this.holder = $( '.qvsfw--has-appear:not(.qvsfw--appeared)' );

			if ( this.holder.length ) {
				this.holder.each(
					function () {
						var $holder = $( this );

						qodeVariationSwatchesForWooCommerce.qodeVariationSwatchesForWooCommerceIsInViewport.check(
							$holder,
							() =>
							{
								qodeVariationSwatchesForWooCommerce.qodeVariationSwatchesForWooCommerceWaitForImages.check(
									$holder,
									function () {
										$holder.addClass( 'qvsfw--appeared' );
									}
								);
							}
						);
					}
				);
			}
		},
	};

	qodeVariationSwatchesForWooCommerce.qodeVariationSwatchesForWooCommerceAppear = qodeVariationSwatchesForWooCommerceAppear;

	var qodeVariationSwatchesForWooCommerceIsInViewport = {
		check: function ( $element, callback, onlyOnce, callbackOnExit ) {
			if ( $element.length ) {
				// When item is 15% in the viewport.
				var offset = typeof $element.data( 'viewport-offset' ) !== 'undefined' ? $element.data( 'viewport-offset' ) : 0.15;

				var observer = new IntersectionObserver(
					function ( entries ) {
						// isIntersecting is true when element and viewport are overlapping.
						// isIntersecting is false when element and viewport don't overlap.
						if ( entries[0].isIntersecting === true ) {
							callback.call( $element );

							// Stop watching the element when it's initialize.
							if ( onlyOnce !== false ) {
								observer.disconnect();
							}
						} else if ( callbackOnExit && onlyOnce === false ) {
							callbackOnExit.call( $element );
						}
					},
					{ threshold: [offset] }
				);

				observer.observe( $element[0] );
			}
		},
	};

	qodeVariationSwatchesForWooCommerce.qodeVariationSwatchesForWooCommerceIsInViewport = qodeVariationSwatchesForWooCommerceIsInViewport;

	/**
	 * Check element images to loaded
	 */
	var qodeVariationSwatchesForWooCommerceWaitForImages = {
		check: function ( $element, callback ) {
			if ( $element.length ) {
				var images       = $element.find( 'img' );
				var images_count = images.length;

				if ( images_count ) {
					var counter = 0;

					for ( var index = 0; index < images_count; index++ ) {
						var img = images[index];

						if ( img.complete ) {
							counter++;

							if ( counter === images_count ) {
								callback.call( $element );
							}
						} else {
							var image = new Image();

							image.addEventListener(
								'load',
								function () {
									counter++;
									if ( counter === images_count ) {
										callback.call( $element );
										return false;
									}
								},
								false
							);
							image.src = img.src;
						}
					}
				} else {
					callback.call( $element );
				}
			}
		},
	};

	qodeVariationSwatchesForWooCommerce.qodeVariationSwatchesForWooCommerceWaitForImages = qodeVariationSwatchesForWooCommerceWaitForImages;

	var qodeVariationSwatchesForWooCommerceScroll = {
		disable: function () {
			if ( window.addEventListener ) {
				window.addEventListener(
					'wheel',
					qodeVariationSwatchesForWooCommerceScroll.preventDefaultValue,
					{ passive: false }
				);
			}

			// window.onmousewheel = document.onmousewheel = qodeVariationSwatchesForWooCommerceScroll.preventDefaultValue;.
			document.onkeydown = qodeVariationSwatchesForWooCommerceScroll.keyDown;
		},
		enable: function () {
			if ( window.removeEventListener ) {
				window.removeEventListener(
					'wheel',
					qodeVariationSwatchesForWooCommerceScroll.preventDefaultValue,
					{ passive: false }
				);
			}
			window.onmousewheel = document.onmousewheel = document.onkeydown = null;
		},
		preventDefaultValue: function ( e ) {
			e = e || window.event;
			if ( e.preventDefault ) {
				e.preventDefault();
			}
			e.returnValue = false;
		},
		keyDown: function ( e ) {
			var keys = [37, 38, 39, 40];
			for ( var i = keys.length; i--; ) {
				if ( e.keyCode === keys[i] ) {
					qodeVariationSwatchesForWooCommerceScroll.preventDefaultValue( e );
					return;
				}
			}
		}
	};

	qodeVariationSwatchesForWooCommerce.qodeVariationSwatchesForWooCommerceScroll = qodeVariationSwatchesForWooCommerceScroll;

})( jQuery );

(function ( $ ) {
	'use strict';

	$( document ).ready(
		function () {
			qvsfwInitVariationSwatches.init();
		}
	);

	$( document ).on(
		'qode_quick_view_for_woocommerce_trigger_quick_view',
		function ( e, $holder ) {
			qvsfwInitVariationSwatches.reInit( $holder );
		}
	);

	$( document ).on(
		'qode_quick_view_for_woocommerce_trigger_woocommerce_select2',
		function () {
			this.holder = $( '.variations_form .value' );
			if ( this.holder.length ) {
				this.holder.each(
					function () {
						qvsfwInitVariationSwatches.destroySelect2( $( this ) );
					}
				);
			}
		}
	);

	var qvsfwInitVariationSwatches = {
		originalData: {},
		productImages: {},
		init: function () {
			this.holder = $( '.variations_form .value' );

			if ( this.holder.length ) {

				this.holder.each(
					function () {
						qvsfwInitVariationSwatches.setActiveLabel( $( this ) );
						qvsfwInitVariationSwatches.changeSelectValue( $( this ) );
						qvsfwInitVariationSwatches.addHelperClasses( $( this ) );
						qvsfwInitVariationSwatches.triggerDisableSelect( $( this ) );
						qvsfwInitVariationSwatches.destroySelect2( $( this ) );
					}
				);
			}

			qvsfwInitVariationSwatches.initVariationsForm();
			qvsfwInitVariationSwatches.handleProductAvailability();
		},
		reInit: function ( $holder ) {
			this.holder = $holder.find( '.variations_form .value' );

			if ( this.holder.length ) {

				this.holder.each(
					function () {
						qvsfwInitVariationSwatches.setActiveLabel( $( this ) );
						qvsfwInitVariationSwatches.changeSelectValue( $( this ) );
						qvsfwInitVariationSwatches.addHelperClasses( $( this ) );
						qvsfwInitVariationSwatches.triggerDisableSelect( $( this ) );
						qvsfwInitVariationSwatches.destroySelect2( $( this ) );
					}
				);
			}

			qvsfwInitVariationSwatches.initVariationsForm();
			qvsfwInitVariationSwatches.handleProductAvailability();
		},
		initVariationsForm: function () {
			this.holder = $( '.variations_form' );

			if ( this.holder.length ) {

				this.holder.each(
					function () {
						qvsfwInitVariationSwatches.handleInitialProductAvailability( $( this ) );
					}
				);
			}
		},
		setActiveLabel: function ( $holder ) {
			var variationsSelect = $holder.find( 'select' );

			if ( variationsSelect.hasClass( 'qvsfw-attribute-type--image' ) || variationsSelect.hasClass( 'qvsfw-attribute-type--color' ) || variationsSelect.hasClass( 'qvsfw-attribute-type--label' ) || variationsSelect.hasClass( 'qvsfw-attribute-type--radio-button' ) || variationsSelect.hasClass( 'qvsfw-attribute-type--tab-switch' ) ) {
				variationsSelect.css( 'display', 'none' );
			}

			if ( variationsSelect.length ) {
				variationsSelect.each(
					function () {
						var select = $( this );

						select.on(
							'change',
							function () {
								var optionSelected = $( this ).find( 'option:selected' ),
									selectedValue  = optionSelected.val();

								qvsfwInitVariationSwatches.setActiveClass(
									$holder,
									selectedValue
								);

								$( document.body ).trigger( 'qode_variation_swatches_for_woocommerce_premium_trigger_variation_select_changed', [ $holder ] );
							}
						);
					}
				);
			}
		},
		setActiveClass: function ( $holder, selectedValue ) {
			var selectOptionsContainer = $holder.find( '.qvsfw-select-options-container' );

			if ( selectOptionsContainer.length ) {
				var options = selectOptionsContainer.find( '.qvsfw-select-option' );

				if ( options.length ) {
					options.each(
						function () {
							var option = $( this );
							option.removeClass( 'qvsfw-selected' );

							var optionValue = option.data( 'value' );
							if ( typeof optionValue === 'number' ) {
								optionValue = optionValue.toString();
							}

							if ( optionValue === selectedValue ) {
								option.addClass( 'qvsfw-selected' );
							}
						}
					);
				}
			}
		},
		changeSelectValue: function ( $holder ) {
			var variationsForm         = $holder.parents( '.variations_form' ).first(),
				variationsSelect       = $holder.find( 'select' ),
				selectOptionsContainer = $holder.find( '.qvsfw-select-options-container' );

			if ( selectOptionsContainer.length ) {
				var options = selectOptionsContainer.find( '.qvsfw-select-option' );

				if ( options.length ) {
					options.each(
						function () {
							var option = $( this );

							option.on(
								'click',
								function ( e ) {
									e.preventDefault();

									var customSelectOptions = variationsForm.find( '.qvsfw-select-option' );
									customSelectOptions.removeClass( 'qvsfw-disable-select' );

									if ( option.hasClass( 'qvsfw-selected' ) ) {
										variationsSelect.val( '' ).change();
									} else {
										variationsSelect.val( option.data( 'value' ) ).change();
									}
								}
							);
						}
					);
				}
			}
		},
		addHelperClasses: function ( $holder ) {
			var tableRow = $holder.closest( 'tr' ),
				select   = $holder.find( 'select' );

			if ( select.length && ! select.hasClass( 'qvsfw-attribute-type--select' ) ) {
				tableRow.addClass( 'qvsfw-attribute-wrapper' );
			}
		},
		triggerDisableSelect: function ( holder ) {
			var form = holder.parents( '.variations_form' ).first();
			form.wc_variation_form();

			form.on(
				'found_variation',
				function ( e, variation ) {
					qvsfwInitVariationSwatches.disableSelect( holder );
				}
			);

			form.on(
				'woocommerce_variation_has_changed',
				function () {
					qvsfwInitVariationSwatches.disableSelect( holder );
				}
			);

			form.on(
				'reset_data',
				function () {
					var customSelectOptions = $( this ).find( '.qvsfw-select-option' );
					customSelectOptions.removeClass( 'qvsfw-disable-select' );
				}
			);
		},
		disableSelect: function ( holder ) {
			var selectedOption  = holder.find( '.qvsfw-selected' ),
				variationsTable = selectedOption.parents( '.variations' ).first(),
				variationRows   = variationsTable.find( '.value' );

			variationRows.each(
				function () {
					var variationRow           = $( this ),
						select                 = variationRow.find( 'select' ),
						selectOptions          = select.find( 'option' ),
						selectOptionsContainer = variationRow.find( '.qvsfw-select-options-container' ),
						customSelectOptions    = variationRow.find( '.qvsfw-select-option' );

					customSelectOptions.removeClass( 'qvsfw-disable-select' );

					// Store custom option values in array.
					var customSelectOptionValueArray = [];
					customSelectOptions.each(
						function () {
							var customSelectOption = $( this );

							var customSelectOptionValue = customSelectOption.data( 'value' );
							if ( typeof customSelectOptionValue === 'number' ) {
								customSelectOptionValue = customSelectOptionValue.toString();
							}

							customSelectOptionValueArray.push( customSelectOptionValue );
						}
					);

					// Store select option values in array.
					var selectOptionValueArray = [];
					selectOptions.each(
						function () {
							var selectOption = $( this );
							if ( selectOption.val() !== '' ) {
								selectOptionValueArray.push( selectOption.val() );
							}
						}
					);

					// Return new array with values of first array (array of custom options values) which don't exist in the second (array of select options values).
					var disabledAttributesArray = $.grep(
						customSelectOptionValueArray,
						function ( x ) {
							return $.inArray(
								x,
								selectOptionValueArray
							) === -1;
						}
					);
					var disabledAttributesCount = disabledAttributesArray.length;
					if ( disabledAttributesCount ) {
						for ( var i = 0; i < disabledAttributesCount; i++ ) {
							var disabledAttribute = selectOptionsContainer.find( '[data-value="' + disabledAttributesArray[i] + '"]' );
							disabledAttribute.addClass( 'qvsfw-disable-select' );
						}
					}
				}
			);
		},
		destroySelect2: function ( holder ) {
			var form   = holder.parents( '.variations_form' ).first(),
				select = form.find( 'select' );

			if ( select.length && ! select.hasClass( 'qvsfw-attribute-type--select' ) ) {
				select.each(
					function () {
						var thisSelect = $( this );

						if ( thisSelect.hasClass( 'select2-hidden-accessible' ) ) {
							thisSelect.select2( 'destroy' );
						}
					}
				);
			}
		},
		handleInitialProductAvailability: function ( variationForm ) {
			if ( qodeVariationSwatchesForWooCommerceGlobal.disableOutOfStockOption === 'yes' ) {
				qvsfwInitVariationSwatches.handleOutOfStock( variationForm );
			}
		},
		handleProductAvailability: function () {

			$( document ).on(
				'found_variation',
				function ( e, variation ) {

					var variationForm               = $( e.target ),
						variationAvailabilityHolder = variationForm.find( '.qvsfw-list-variation-availability' ),
						product                     = variationForm.parents( '.product' ).first(),
						addToCart                   = product.find( '.add_to_cart_button' );

					if ( addToCart.hasClass( 'disabled qvsfw-disabled' ) ) {
						addToCart.removeClass( 'disabled qvsfw-disabled' );
					}

					if ( qodeVariationSwatchesForWooCommerceGlobal.productAvailabilityOption === 'yes' ) {

						if ( variationForm.hasClass( 'qvsfw-list-variations-form' ) && variationAvailabilityHolder.length ) {
							variationAvailabilityHolder.html( $( variation.availability_html ) );
						}
					}

					if ( ! variation.is_in_stock ) {
						addToCart.addClass( 'disabled qvsfw-disabled' );
					}
				}
			);

			$( document ).on(
				'reset_data',
				function ( e ) {
					var variationForm               = $( e.target ),
						variationAvailabilityHolder = variationForm.find( '.qvsfw-list-variation-availability' ),
						stock                       = variationAvailabilityHolder.find( '.stock' ),
						product                     = variationForm.parents( '.product' ).first(),
						addToCart                   = product.find( '.add_to_cart_button' );

					if (stock.length) {
						stock.remove();
					}

					if ( addToCart.hasClass( 'disabled qvsfw-disabled' ) ) {
						addToCart.removeClass( 'disabled qvsfw-disabled' );
					}
				}
			);

			if ( qodeVariationSwatchesForWooCommerceGlobal.disableOutOfStockOption === 'yes' ) {

				$( document ).on(
					'woocommerce_variation_has_changed',
					function ( e ) {
						var variationForm = $( e.target );

						qvsfwInitVariationSwatches.handleOutOfStock( variationForm );
					}
				);
			}
		},
		handleOutOfStock: function ( variationForm ) {

			if ( variationForm.hasClass( 'qvsfw-list-variations-form' ) && qodeVariationSwatchesForWooCommerceGlobal.enableAjaxInLoop === 'yes' ) {
				return;
			}

			var variationData      = variationForm.data( 'product_variations' ),
				select 			   = variationForm.find( 'select' ),
				options            = variationForm.find( '.qvsfw-select-option' ),
				selectedAttributes = {};

			if ( options.length ) {
				options.each(
					function () {
						var option        = $( this ),
							holder        = option.parents( '.value' ).first(),
							select        = holder.find( 'select' ),
							attributeName = select.data( 'attribute_name' ),
							value         = select.val();

						selectedAttributes[attributeName] = value;
					}
				);
			}

			var variationsAttributesArray = [];

			if ( select.length ) {

				if ( ! variationForm.hasClass( 'qvsfw-list-variations-form' ) ) {
					select.each(
						function () {
							var atrName 	  = $( this ).data( 'attribute_name' ),
								selectOptions = $( this ).find( 'option' );

							var optionsArray = [];
							selectOptions.each(
								function () {
									if ( $( this ).val() !== '' ) {
										optionsArray.push( $( this ).val() );
									}
								}
							);

							// Push attribute name and values into variationsAttributesArray.
							variationsAttributesArray.push(
								{
									attribute_name: atrName,
									values: optionsArray
								}
							);
						}
					);
				} else {
					var variationAttributes = variationForm.data( 'variation_attributes' );

					variationsAttributesArray = [];

					$.each(
						variationAttributes,
						function ( key, value ) {
							var obj 		   = {};
							obj.attribute_name = 'attribute_' + key;
							obj.values 		   = value instanceof Array ? value : Object.values( value );
							variationsAttributesArray.push( obj );
						}
					);
				}

				$.each(
					select,
					function () {
						var thisSelect             = $( this ),
							allSelectOptions       = [],
							selectOptionsContainer = thisSelect.parent().find( '.qvsfw-select-options-container' );

						thisSelect.find( 'option' ).each(
							function () {
								var optionValue = $( this ).val();

								if ( optionValue.length ) {
									allSelectOptions.push( optionValue );
								}
							}
						);

						$.each(
							selectOptionsContainer,
							function () {
								var thisSelectOptionsContainer = $( this );

								thisSelectOptionsContainer.find( '.qvsfw-select-option' ).each(
									function () {
										var val = $( this ).data( 'value' ) + '';

										var holder         = $( this ).parents( '.value' ).first(),
											select         = holder.find( 'select' ),
											optionAttrName = select.data( 'attribute_name' );

										var chosenAttr = Object.assign( {}, selectedAttributes );
										var inStock    = true;

										var selectOptionsContainers = variationForm.find( '.qvsfw-select-options-container' );

										if ( $.inArray( val, allSelectOptions ) === '-1' ) {
											$( this ).addClass( 'qvsfw-disable-out-of-stock' );

											if ( selectOptionsContainers.length === 1 || selectOptionsContainers.length === 2 ) {
												$( this ).append( '<span class="qvsfw-notification-inactive">' + qodeVariationSwatchesForWooCommerceGlobal.iconNotify + '</span>' );
											}
										} else {
											$( this ).removeClass( 'qvsfw-disable-out-of-stock' );
											$( this ).find( '.qvsfw-notification-inactive' ).remove();
										}

										chosenAttr[optionAttrName] = val;

										var newVariationDataArray = [];
										var processedAttributes   = {};

										// Iterate through variationData to ensure all possible variations are included,
										// filling in missing attribute values with all available options.
										$.each(
											variationData,
											function ( index, item ) {

												$.each(
													variationsAttributesArray,
													function ( index, attribute ) {

														if ( item.attributes[attribute.attribute_name] === '' ) {

															$.each(
																attribute.values,
																function ( index, value ) {
																	// Deep copy of the original item.
																	var newItem = $.extend( true, {}, item );
																	// Deep copy of the attributes object.
																	newItem.attributes = $.extend( true, {}, item.attributes );
																	// Modify the attribute.
																	newItem.attributes[attribute.attribute_name] = value;

																	var attributeKey = JSON.stringify( newItem.attributes );
																	if ( ! processedAttributes[attributeKey] ) {
																		processedAttributes[attributeKey] = true;
																		newVariationDataArray.push( newItem );
																	}
																}
															);
														} else {
															var attributeKey = JSON.stringify( item.attributes );
															if ( ! processedAttributes[attributeKey] ) {
																processedAttributes[attributeKey] = true;
																newVariationDataArray.push( item );
															}
														}
													}
												);
											}
										);

										var filteredVariationData = newVariationDataArray.filter(
											function ( item ) {
												// Check if any attribute in the object has an empty value.
												for ( var key in item.attributes ) {
													if ( item.attributes.hasOwnProperty( key ) && item.attributes[key] === '' ) {
														// Don't include if any attribute has an empty value.
														return false;
													}
												}
												// Include if all attributes have values.
												return true;
											}
										);

										variationData = filteredVariationData;

										var variations = qvsfwInitVariationSwatches.findMatchingVariations(
											variationData,
											chosenAttr
										);

										if ( variations.length ) {
											inStock = variations.find(
												function ( variation ) {
													return variation.is_in_stock === true;
												}
											);
										}

										if ( ! inStock ) {
											$( this ).addClass( 'qvsfw-disable-out-of-stock' );

											if ( selectOptionsContainers.length === 1 || selectOptionsContainers.length === 2 ) {
												$( this ).append( '<span class="qvsfw-notification-inactive">' + qodeVariationSwatchesForWooCommerceGlobal.iconNotify + '</span>' );
											}
										} else {
											$( this ).removeClass( 'qvsfw-disable-out-of-stock' );
											$( this ).find( '.qvsfw-notification-inactive' ).remove();
										}
									}
								);
							}
						);
					}
				);
			}
		},
		findMatchingVariations: function ( variations, attributes ) {
			var matching = [];
			$.each(
				variations,
				function ( index, variation ) {
					if ( qvsfwInitVariationSwatches.isMatch(
						variation.attributes,
						attributes
					) ) {
						matching.push( variation );
					}
				}
			);
			return matching;
		},
		isMatch: function ( variationAttributes, attributes ) {
			var match = true;
			for ( var attrName in variationAttributes ) {
				if ( variationAttributes.hasOwnProperty( attrName ) ) {
					var val1 = variationAttributes[attrName],
						val2 = attributes[attrName];

					if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
						match = false;
					}
				}
			}
			return match;
		}
	};

	qodeVariationSwatchesForWooCommerce.qvsfwInitVariationSwatches = qvsfwInitVariationSwatches;

})( jQuery );
