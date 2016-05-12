<?php

$meatGeneral = array(
				/*'price-filter' => array(
								'label' => __( 'Price Filter', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_price_filter',
								'type' => 'checkbox',
								'default' => '*.jpg;*.png',
								'options' => array(
												 'yes' => __( 'Enable', 'nm-opw' ) 
								),
								'help' => __( 'Tick this to enable price filter slider on frontend.', 'nm-opw' ) 
				),*/

				'search-label' => array(
								'label' => __( 'Search Placeholder', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_search_placeholder',
								'type' => 'text',
								'default' => 'Type to filter...',
								'help' => __( 'Placeholder text for search input.', 'nm-opw' ) 
				),

				'product-count' => array(
								'label' => __( 'Product Count', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_product_count',
								'type' => 'checkbox',
								'default' => 'yes',
								'options' => array(
												 'yes' => __( 'Show', 'nm-opw' ) 
								),
								'help' => __( 'Tick this to show products count with category names.', 'nm-opw' ) 
				),

				'sticky-store-button' => array(
								'label' => __( 'Sticky Store Button', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_sticky_btn',
								'type' => 'checkbox',
								'default' => 'yes',
								'options' => array(
												 'yes' => __( 'Show', 'nm-opw' ) 
								),
								'help' => __( 'Tick this to show sticky button for store.', 'nm-opw' ) 
				),

				'sticky-button-text' => array(
								'label' => __( 'Sticky Button Text', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_sticky_btn_text',
								'type' => 'text',
								'default' => 'Shop',
								'help' => __( 'Label for sticky button.', 'nm-opw' ) 
				),

				'sticky-button-url' => array(
								'label' => __( 'Sticky Button Url', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_sticky_btn_url',
								'type' => 'text',
								'default' => 'Shop',
								'help' => __( 'Url of that page where you used [ng-woostore].', 'nm-opw' ) 
				),
);


$meatDesignLayout = array(
				'items-in-row' => array(
								'label' => __( 'Number of items in row', 'nm-opw' ),
								'desc' => __( 'It is background color for top bar buttons', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_items_in_row',
								'type' => 'select',
								'default' => __( 'Select Columns', 'nm-opw' ),
								'help' => __( 'Number of columns in a row for Grid view', 'nm-opw' ),
								'options' => array(
												'6' => __( '2 Items', 'nm-opw' ),
												'4' => __( '3 Items', 'nm-opw' ), 
												'3' => __( '4 Items', 'nm-opw' ),
												'2' => __( '6 Items', 'nm-opw' ),
								)
				),
				
				'static-bg-color' => array(
								'label' => __( 'Background Color (Static)', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_static_bg_color',
								'type' => 'color',
								'default' => '#FFFFFF',
								'help' => __( 'Static background color for Buttons, Pagination and Count.', 'nm-opw' ),
				),
				
				'static-text-color' => array(
								'label' => __( 'Text Color (Static)', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_static_text_color',
								'type' => 'color',
								'default' => '#FFFFFF',
								'help' => __( 'Static text color for Buttons, Pagination and Count.', 'nm-opw' ),
				),
				
				'hover-bg-color' => array(
								'label' => __( 'Background Color (Hover)', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_hover_bg_color',
								'type' => 'color',
								'default' => '#FFFFFF',
								'help' => __( 'On hover background color for Buttons and Pagination.', 'nm-opw' ),
				),
				
				'hover-text-color' => array(
								'label' => __( 'Text Color (Hover)', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_hover_text_color',
								'type' => 'color',
								'default' => '#FFFFFF',
								'help' => __( 'On hover text color for Buttons and Pagination.', 'nm-opw' ),
				),
				
				// 'font-size' => array(
				// 				'label' => __( 'Font Size', 'nm-opw' ),
				// 				'id' => $this->plugin_meta[ 'shortname' ] . '_font_size',
				// 				'type' => 'text',
				// 				'default' => '12px',
				// 				'help' => __( 'Font size for all buttons and pagination along with units. eg(15px).', 'nm-opw' ),
				// ),
);

$meatCartCheckout = array(
				
				'checkout-button' => array(
								'label' => __( 'Checkout Button', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_checkout_btn',
								'type' => 'checkbox',
								'default' => 'yes',
								'options' => array(
												 'yes' => __( 'Show', 'nm-opw' ) 
								),
								'help' => __( 'Tick this to show checkout button.', 'nm-opw' ) 
				),

				'clear-cart-btn' => array(
								'label' => __( 'Clear Cart Button', 'nm-opw' ),
								'desc' => __( '', 'nm-opw' ),
								'id' => $this->plugin_meta[ 'shortname' ] . '_clear_cart_btn',
								'type' => 'checkbox',
								'default' => 'yes',
								'options' => array(
												 'yes' => __( 'Show', 'nm-opw' ) 
								),
								'help' => __( 'Tick this to show clear cart button on Cart page.', 'nm-opw' ) 
				),
);

$meatShortcode = array(
				'shortcode-file'	=> array(	
								'label' => 'Shortcode',
								'type'		=> 'file',
								'id'		=> 'generate-shortcode.php',
				),

);



$this->the_options = array(
				'general_settings' => array(
								'name' => __( 'General Settings', 'nm-opw' ),
								'type' => 'tab',
								'desc' => __( 'Set options as per your need', 'nm-opw' ),
								'meat' => $meatGeneral 
								
				),
				'styles_settings' => array(
								'name' => __( 'Design and Layout', 'nm-opw' ),
								'type' => 'tab',
								'desc' => __( 'Set message as per your need', 'nm-opw' ),
								'meat' => $meatDesignLayout 
								
				),
				'cart_checkout' => array(
								'name' => __( 'Cart and Checkout', 'nm-opw' ),
								'type' => 'tab',
								'desc' => __( 'Set message as per your need', 'nm-opw' ),
								'meat' => $meatCartCheckout 
								
				),
				'shortcode' => array(
								'name' => __( 'Shortcode', 'nm-opw' ),
								'type'		=> 'tab',
								'desc' => __( 'Set message as per your need', 'nm-opw' ),
								'meat' => $meatShortcode,
								
				),
				
				
);

//print_r($repo_options);