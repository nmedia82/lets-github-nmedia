<?php
/*
 * this is main plugin class
*/


/* ======= the model main class =========== */
if(!class_exists('NM_Framwork_V2_nm_opw')){
	$_framework = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'nm-framework.php';
	if( file_exists($_framework))
		include_once($_framework);
	else
		die('Reen, Reen, BUMP! not found '.$_framework);
}


/*
 * [1]
 * TODO: change the class name of your plugin
 */
class NM_OnePageWoo extends NM_Framwork_V2_nm_opw{

	private static $ins = null;
	
	public static function init()
	{
		add_action('plugins_loaded', array(self::get_instance(), '_setup'));
	}
	
	public static function get_instance()
	{
		// create a new object if it doesn't exist.
		is_null(self::$ins) && self::$ins = new self;
		return self::$ins;
	}
	
	
	function _setup(){
		
		//setting plugin meta saved in config.php
		$this -> plugin_meta = get_plugin_meta_nm_opw();

		//getting saved settings
		$this -> plugin_settings = get_option ( $this -> plugin_meta['shortname'] . '_settings' );
		
		
		/*
		 * [2]
		 * TODO: update scripts array for SHIPPED scripts
		 * only use handlers
		 */
		//setting shipped scripts
		$this -> wp_shipped_scripts = array('jquery');
		
		
		/*
		 * [3]
		* TODO: update scripts array for custom scripts/styles
		*/
		//setting plugin settings
		$this -> plugin_scripts =  array(array(	'script_name'	=> 'scripts',
												'script_source'	=> '/js/script.js',
												'localized'		=> true,
												'type'			=> 'js',
												'depends'		=> array('jquery'),
												'in_footer'		=> false,
												'version'		=> false,
										),
										array(	'script_name'	=> 'styles',
														'script_source'	=> '/plugin.styles.css',
														'localized'		=> false,
														'type'			=> 'style',
														'in_footer'		=> false,
														'version'		=> false,
										),
										array(	'script_name'	=> 'font-awesome',
														'script_source'	=> '/font-awesome/css/font-awesome.min.css',
														'localized'		=> false,
														'type'			=> 'style',
														'in_footer'		=> false,
														'version'		=> false,
										),
									);
		
		/*
		 * [4]
		* TODO: localized array that will be used in JS files
		* Localized object will always be your pluginshortname_vars
		* e.g: pluginshortname_vars.ajaxurl
		*/
		$this -> localized_vars = array(	'ajaxurl' 		=> admin_url( 'admin-ajax.php', (is_ssl() ? 'https' : 'http') ),
											'plugin_url' 	=> $this->plugin_meta['url'],
											'plugin_doing'	=> $this->plugin_meta['url'] . 'images/loading.gif',
											'settings'		=> $this -> plugin_settings
										);
		
		
		/*
		 * [5]
		 * TODO: this array will grow as plugin grow
		 * all functions which need to be called back MUST be in this array
		 * setting callbacks
		 * Updated V2: September 16, 2014
		 * Now truee/false against each function
		 * true: logged in
		 * false: visitor + logged in
		 */
		 
		//following array are functions name and ajax callback handlers
		$this -> ajax_callbacks = array('get_single_product'	=> false,
										'add_to_cart'			=> false,
										'get_cart_template'			=> false,
										'delete_item_from_cart'			=> false,
										'apply_coupon_code'			=> false,
										'remove_coupon_code'			=> false,
										'get_checkout_template'			=> false,
										'empty_cart'			=> false,
										);	//do not change this action, is for admin
										
		
		/*
		 * plugin localization being initiated here
		 */
		add_action('init', array($this, 'wpp_textdomain'));
		
		
		/*
		 * plugin main shortcode if needed
		 */
		add_shortcode('ng-woostore', array($this , 'render_shortcode_template'));
		
		
		/*
		 * hooking up scripts for front-end
		*/
		add_action('wp_enqueue_scripts', array($this, 'load_scripts'));

		/*
		 * hooking sticky shop button
		*/
		add_action('wp_footer', array($this, 'shop_sticky_button'));

		/**
		 * manipulating checkout page as true
		 */
		add_filter('woocommerce_is_checkout', array($this, 'nm_opw_is_checkout'), 99, 1 );

		/*
		 * registering callbacks
		*/
		$this -> do_callbacks();
	}
	

	function nm_opw_is_checkout( $value ){

		return true;
	}
	
	function get_plugin_settings(){
		
		$temp_settings = array();
		foreach($this -> plugin_setting_tabs as $tab){
			
			$temp_settings[$tab] = get_option( $tab . '_settings' );
		}
		
		$this -> pa($temp_settings);
		
		return $temp_settings;
	}
	
	function load_scripts(){
		wp_register_script( 'angular-js', $this->plugin_meta['url'] .'js/angular.min.js', array('jquery'));
		wp_register_script( 'nm-script', $this->plugin_meta['url'] .'js/ngwoostore.js', array('angular-js'));
		wp_register_style( 'font-awesome', $this->plugin_meta['url'] .'font-awesome/css/font-awesome.min.css' );
		wp_register_style( 'nm-styles', $this->plugin_meta['url'] .'plugin.styles.css', array('font-awesome') );

		//wp_enqueue_script( 'wc-checkout');
	}
	
	/*
	 * =============== NOW do your JOB ===========================
	 * 
	 */
	
	
	
	/*
	 * rendering template against shortcode
	*/
	function render_shortcode_template($atts){

		$args = array(
		    'hide_empty'        => true,
		    'orderby'    => 'title',
    		'order'      => 'ASC',
		);

		if (isset($atts['include'])) {
			$include = explode(",",$atts['include']);
			$args = array(
			    'hide_empty'        => true,
			    'include'           => $include,
			);
		} else if (isset($atts['exclude']))  {
			$exclude = explode(",",$atts['exclude']);
			$args = array(
			    'hide_empty'        => true, 
			    'exclude'           => $exclude,
			);
		}

		// Woo Scripts
		$single_product_script = WC()->plugin_url() . '/assets/js/frontend/single-product.min.js';
        $single_vari_script = WC()->plugin_url() . '/assets/js/frontend/add-to-cart-variation.min.js';
        $checkout_script = WC()->plugin_url() . '/assets/js/frontend/checkout.min.js';
        $pretty_photo = WC()->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init.min.js';

		wp_enqueue_script( 'wc-single-product', WC()->plugin_url() . '/assets/js/frontend/single-product.min.js', array( 'jquery' ), WC()->version, true );
		wp_enqueue_script( 'wc-add-to-cart-variation', WC()->plugin_url() . '/assets/js/frontend/add-to-cart-variation.min.js', array( 'jquery' ), WC()->version, true );
		wp_enqueue_style( 'wc-pretty-photo-css', WC()->plugin_url() . '/assets/css/prettyPhoto.css');
		wp_enqueue_script( 'pretty-photo-js', WC()->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.min.js', array( 'jquery' ), WC()->version, true );
		wp_enqueue_script( 'pretty-photo-js-init', WC()->plugin_url() . '/assets/js/prettyPhoto/jquery.prettyPhoto.init.min.js', array( 'jquery' ), WC()->version, true );


		wp_enqueue_script( 'nm-script' );
		wp_enqueue_style( 'nm-styles' );

	$product_categories = get_terms( 'product_cat', $args );

	$count = count($product_categories);

	if ( $count > 0 ){
	    foreach ( $product_categories as $product_category ) {
	    	$categories[] = array(
			    			'name' => $product_category->name,
			    			'desc' => $product_category->description,
			    			'id' => $product_category->term_id,
			    			'totalproducts' => $product_category->count,
			    			'thumbnail' => wp_get_attachment_url(get_woocommerce_term_meta($product_category->term_id, 'thumbnail_id', true)),
			    			'products' => $this->get_cat_products($product_category->slug),
			    		);
	    }

	    wp_localize_script( 'nm-script', 'woostore', array(
	    									'cats' => $categories,
	    									'ajaxurl' => admin_url( 'admin-ajax.php' ),
	    									'tabscript' => $single_product_script,
	    									'variation' => $single_vari_script,
	    									'checkout' => $checkout_script,
	    									'update_shipping_method_nonce' => wp_create_nonce( "update-shipping-method" ),
	    									'pretty_photo' => $pretty_photo,
	    									)
	    );
	}

		ob_start();

		$template_vars = array('user_name'	=> 'Testing the vars');
		$this -> load_template('_render_shop.php', $template_vars);

		$output_string = ob_get_contents();
		ob_end_clean();
			
		return $output_string;
	}

	function get_cat_products($cat_id){
		$args = array(
            'posts_per_page' => -1,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    // 'terms' => 'white-wines'
                    'terms' => $cat_id
                )
            ),
            'post_type' => 'product',
            'orderby' => 'title,'
        );

        $products = new WP_Query( $args );
        while ( $products->have_posts() ) {
            $products->the_post();
            $_product = wc_get_product( get_the_id() );
            $all_products[] = array(
            			'name' => get_the_title(),
            			'id' => get_the_id(),
            			'thumbnail' => get_the_post_thumbnail( get_the_id(), 'medium' ),
            			'excerpt' => get_the_excerpt(),
            			'price' => $_product->get_price_html(),
            			'sale_price' => ($_product->get_sale_price()) ? true : false ,
            			);
        }

        return $all_products;
	}

	function get_single_product(){
		//setting the product_id
        $this -> product_id = $_REQUEST['product_id'];
        
        /* ============ this block is to make plugin compatibel with other plugins ======== */
            
            // first thing is first, checking if PPOM plugin is activated.
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            // check for plugin using plugin name
            if ( is_plugin_active( 'nm-woocommerce-personalized-product/index.php' ) ) {
              
                global $nmpersonalizedproduct;
                
                remove_action ( 'woocommerce_before_add_to_cart_button', array (
        				$nmpersonalizedproduct,
        				'render_product_meta' 
        		), 15 );
        		
        		//check plugin if meta is attached
        		$meta_attched = get_post_meta ( $this -> product_id, '_product_meta_id', true );
        		if(isset($meta_attched) && $meta_attched != 0){
        		    add_action('woocommerce_after_add_to_cart_form', array($this, 'redirect_link_product_page'));
        		    
        		    //now hiding the add to cart button
        		    echo '<script type="text/javascript">jQuery("form.cart").hide();</script>';
        		}
            }
            
            if ( is_plugin_active('woo-product-designer/index.php')) {
              
                global $nm_wcpd;
                
                remove_action ( 'woocommerce_before_add_to_cart_button', array (
        				$nm_wcpd,
        				'render_product_designer' 
        		), 15 );
        		
        		//check plugin if meta is attached
        		$design_attched = get_post_meta ( $this -> product_id, 'wcpd_design_id', true );
        		if(isset($design_attched) && $design_attched != 0){
        		    add_action('woocommerce_after_add_to_cart_form', array($this, 'redirect_link_product_page'));
        		    
        		    //now hiding the add to cart button
        		    echo '<script type="text/javascript">jQuery("form.cart").hide();</script>';
        		}
            } 
                        
            
        /* ============ --block ends-- ======== */
        
        global $product, $post;
        
        $args = array('p'   => $this -> product_id, 'post_type' => array('product'));

        $thepost = new WP_Query( $args );

        if ( $thepost->have_posts() ) : while ( $thepost->have_posts() ) : $thepost -> the_post();
        
        $GLOBALS['withcomments'] = 1;

        ob_start();

        $woocommerce_product_template = $this -> get_single_product_template();
        if( file_exists($woocommerce_product_template) ){
            include_once( $woocommerce_product_template);
        }else{
            die('help me to find '.$woocommerce_product_template);
        }

        $output_string = ob_get_contents();
        ob_end_clean();
                
        echo $output_string;
        endwhile;
        else:
        endif;

        wp_reset_query();

        die(0);
	}

	/**
	* this function will search woocommerce for current theme other use woocommerce plugin template file
	* */
	function get_single_product_template(){

		// first checking in theme
		$single_product_template = get_template_directory() . '/woocommerce/templates/content-single-product.php';

		if( file_exists($single_product_template) ){
			return $single_product_template;
		} else {
			// if not found in theme then return woocommerce plugin
			return WC()->plugin_path() . '/templates/content-single-product.php';
		}


	}

	/*
	 * Get Full Cart Template
	 */

	function get_cart_template(){

		
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		

		WC()->cart->calculate_totals();

		if ( WC()->cart->get_cart_contents_count() == 0 ) {
		    echo '<p>Your cart is currently empty.</p><br><a class="button nmreturn">Return to Shop</a>';
		} else {
			$path = WC()->plugin_path() . '/templates/cart/cart.php';
			echo load_template( $path );
		}

		die(0);
	}

	/*
	 * Get Checkout Template
	 */

	function get_checkout_template(){

		
		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			define( 'WOOCOMMERCE_CHECKOUT', true );
		}

		// Get checkout object
		$checkout = WC()->checkout();
		wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );

		// $path = WC()->plugin_path() . '/templates/checkout/form-checkout.php';

		// echo load_template( $path );
		die(0);
	}

	/*
	 * Apply coupon
	 */
	function apply_coupon_code(){
		extract($_REQUEST);

		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		WC()->cart->add_discount( $code );
		
		WC()->cart->calculate_totals();

		$path = WC()->plugin_path() . '/templates/cart/cart.php';
		echo load_template( $path );
		die(0);
	}

	/*
	 * Remove coupon
	 */
	function remove_coupon_code(){
		extract($_REQUEST);

		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		WC()->cart->remove_coupon( $code );

		WC()->cart->calculate_totals();

		$path = WC()->plugin_path() . '/templates/cart/cart.php';
		echo load_template( $path );
		die(0);
	}

	/*
	 * add an item in cart with quantity and update on menu
	 */
	
	function add_to_cart(){
		parse_str($_REQUEST['formData']);


		if (isset($variation_id)) {
			
			$wc_variation = json_decode( stripslashes( $variation ), true );
			//var_dump( $wc_variation ); exit;

			WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $wc_variation );
			
		} else {

			WC()->cart->add_to_cart( $product_id, $quantity );
		}

		WC()->cart->calculate_totals();
		
	 	echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'nm-opw'),
		WC()->cart->cart_contents_count) . ' - '.WC()->cart->get_cart_total();
		die(0);
	}

	/*
	 * delete item from cart
	 */
	
	function delete_item_from_cart(){
		extract($_REQUEST);

		WC()->cart->remove_cart_item( $key );

		// Updating menu button
		echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'nm-opw'),
		WC()->cart->cart_contents_count) . ' - '.WC()->cart->get_cart_total();
		die(0);

	}

	/*
	 * empty cart
	 */
	
	function empty_cart(){
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		

		WC()->cart->empty_cart();

		if ( WC()->cart->get_cart_contents_count() == 0 ) {
		    echo '<p>Your cart is currently empty.</p><br><a class="button nmreturn">Return to Shop</a>';
		} else {
			$path = WC()->plugin_path() . '/templates/cart/cart.php';
			echo load_template( $path );
		}

		die(0);

	}

	function shop_sticky_button(){

		$stickyButton 			=  ($this -> get_option('_sticky_btn') == 'yes') ? true : false ;
		$stickyButtonText 		=  ($this -> get_option('_sticky_btn_text') != '') ? $this -> get_option('_sticky_btn_text') : 'Quick Store' ;
		$stickyButtonUrl 		=  ($this -> get_option('_sticky_btn_url') != '') ? $this -> get_option('_sticky_btn_url') : '#' ;		

		if ($stickyButton) {
			echo '<a href="'.$stickyButtonUrl.'" class="nm-button stickybutton" style="position: fixed; right: 10px; bottom: 10px;">'.$stickyButtonText.'</a>';
		}

		echo "<style>.nm-button {
				border: 0 none;
				border-radius: 2px 2px 2px 2px;
				color: #FFFFFF;
				cursor: pointer;
				font-family: Arial,sans-serif;
				font-size: 12px;
				font-weight: bold;
				line-height: 20px;
				margin-bottom: 0;
				margin-top: 10px;
				padding: 7px 10px;
				text-transform: none;
				transition: all 0.3s ease 0s;
				-moz-transition: all 0.3s ease 0s;
				-webkit-transition: all 0.3s ease 0s;
				display: block;
				margin: 0;
				width: auto;
				text-align: center; /* DELETE WHEN WIDTH AUTO */
				background: none repeat scroll 0 0 #444444;
			    color: #FFFFFF;
			    text-decoration: none !important;
			}</style>";
	}
	
	
	function redirect_link_product_page(){
		
		global $product;
		echo '<a href="'.esc_url( get_permalink($product->id) ).'" class="single_add_to_cart_button button">'.__('Select options', 'woocommerce').'</a>';
	}
	// ================================ SOME HELPER FUNCTIONS =========================================

	
	

	function activate_plugin(){

		
		
		/*
		 * NOTE: $plugin_meta is not object of this class, it is constant 
		 * defined in config.php
		 */
			
		/* global $wpdb,$plugin_meta;
		
		$sql = "";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		add_option("nm_plugin_db_version", $plugin_meta['db_version']); */

	}

	function deactivate_plugin(){

		//do nothing so far.
	}
	
}