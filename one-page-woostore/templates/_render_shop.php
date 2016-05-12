<div ng-app="nmWoostore" class="nm-woostore" id="page">
	<div ng-controller="wooCtrl">
		<div class="nm-loader"></div>
<?php

	/* Taking Values from Options and Setting Defaults */
	$priceFilter 			=  ($this -> get_option('_price_filter') == 'yes') ? true : false ;
	$searchPlaceholder 		=  ($this -> get_option('_search_placeholder') != '') ? $this -> get_option('_search_placeholder') : 'Search Here...' ;
	$productCount 			=  ($this -> get_option('_product_count') == 'yes') ? true : false ;

	$itemsInRow 			=  ($this -> get_option('_items_in_row') != '') ? $this -> get_option('_items_in_row') : 3 ;
	$staticBgColor 			=  ($this -> get_option('_static_bg_color') != '') ? $this -> get_option('_static_bg_color') : '#444444' ;
	$staticTextColor 		=  ($this -> get_option('_static_text_color') != '') ? $this -> get_option('_static_text_color') : '#FFFFFF' ;
	$hoverBgColor 			=  ($this -> get_option('_hover_bg_color') != '') ? $this -> get_option('_hover_bg_color') : '#2DB6CF' ;
	$hoverTextColor 		=  ($this -> get_option('_hover_text_color') != '') ? $this -> get_option('_hover_text_color') : '#FFFFFF' ;
	$fontSize 				=  ($this -> get_option('_font_size') != '') ? $this -> get_option('_font_size') : '12px' ;

	$checkoutBtn 			=  ($this -> get_option('_checkout_btn') == 'yes') ? true : false ;
	$clearCartBtn 			=  ($this -> get_option('_clear_cart_btn') == 'yes') ? true : false ;

?>
<style>
	.nm-button, .nm-breadcrumb, .nm-woostore .productprice, .nm-woostore #sliderBlock, .nm-btn-bar #search {
		background-color: <?php echo $staticBgColor; ?> !important;
		color: <?php echo $staticTextColor; ?> !important;
	}
	.nm-button:hover, .nm-btn-bar #search:hover {
		background-color: <?php echo $hoverBgColor; ?> !important;
		color: <?php echo $hoverTextColor; ?> !important;
	}
	input::-webkit-input-placeholder {
	   color: white;
	}

	input:-moz-placeholder { /* Firefox 18- */
	   color: white;  
	}

	input::-moz-placeholder {  /* Firefox 19+ */
	   color: white;  
	}

	input:-ms-input-placeholder {  
	   color: white;  
	}	
</style>

		<!-- Breadcrumb -->
		
		<div class="nm-breadcrumb">
			<a ng-click="activeTab = 'categories'"><i><?php _e( 'Shop', 'nm-opw' ); ?></i></a>
			<span ng-show="activeTab == 'allproducts' || activeTab == 'singleproduct'">
				<i class="fa fa-angle-double-right"></i><a ng-click="activeTab = 'allproducts'"> <i>{{currentCategory}}</i></a>
			</span>
			<span ng-show="activeTab == 'singleproduct'">
				<i class="fa fa-angle-double-right"></i> <i>{{currentProduct}}</i>
			</span>
			<span ng-show="activeTab == 'carttemplate'">
				<i class="fa fa-angle-double-right"></i> <i><?php _e( 'Cart', 'nm-opw' ); ?></i>
			</span>
			<span ng-show="activeTab == 'checkouttemplate'">
				<i class="fa fa-angle-double-right"></i> <i><?php _e( 'Checkout', 'nm-opw' ); ?></i>
			</span>
		</div>

		<!-- Top Right Menu Buttons -->
		
		<ul class="nm-btn-bar">
			<?php /*global $woocommerce;*/ if($checkoutBtn){
				echo '<li ng-hide="activeTab == \'checkouttemplate\'">
					<a class="nm-button" ng-click="loadCheckoutPage()"><i class="fa fa-check"></i> Checkout</a>
				</li>';
			} ?>
			<li ng-show="currentCartTotal != '' && activeTab != 'carttemplate'" ng-click="loadCartContents()">
				<a class="nm-button"><i class="fa fa-shopping-cart"></i>
					<span ng-bind-html="printHTML(currentCartTotal)" class="totalprice"></span>
				</a>
			</li>
			<li ng-show="currentCartTotal == '' && activeTab != 'carttemplate'" ng-click="loadCartContents()">
				<a class="nm-button">
					<i class="fa fa-shopping-cart"></i>
					<span class="totalprice"><?php echo sprintf(_n('%d item', '%d items', WC()->cart->cart_contents_count, 'nm-opw'), WC()->cart->cart_contents_count) . ' - '.WC()->cart->get_cart_total(); ?></span>
				</a>
			</li>
			<li class="search" ng-show="activeTab == 'allproducts' || activeTab == 'categories'">
				<span class="icon"><i class="fa fa-search"></i></span>
				<input type="search" id="search" placeholder="<?php echo $searchPlaceholder; ?>" ng-model="search.name" />			
			</li>
			<li ng-show="activeTab == 'allproducts'">
				<label id="sliderLabel">
				    <input type="checkbox" ng-model="search.sale_price" />
				    <span id="slider">
				        <span id="sliderOn">Sale</span>
				        <span id="sliderOff">All</span>
				        <span id="sliderBlock"></span>
				    </span>
				</label>				
			</li>
			<li ng-show="activeTab == 'allproducts' || activeTab == 'categories'">
				<a class="nm-button" ng-click="layout = 'inline'"><i class="fa fa-list-ul"></i></a>
			</li>
			<li ng-show="activeTab == 'allproducts' || activeTab == 'categories'">
				<a class="nm-button" ng-click="layout = 'grid'"><i class="fa fa-th"></i></a>
			</li>
			<?php if($clearCartBtn){
				echo '<li ng-show="activeTab == \'carttemplate\'">
					<a class="nm-button" ng-click="emptyCart()"><i class="fa fa-trash"></i> Clear Cart</a>
				</li>';
			} ?>
		</ul>

		<div class="clearfix"></div>
		

		<div class="nm-content-wrapper woocommerce-page woocommerce" ng-switch="activeTab">
		<div class="woocommerce-message"></div>

			<!-- Categories -->

			<div ng-switch-when="categories" ng-init="search.sale_price = ''">
				<div class="grid grid-pad">
				    <div ng-class="{ 'col-<?php echo $itemsInRow; ?>-12': layout == 'grid' , 'inline': layout == 'inline' }" ng-repeat="singlecat in allcats | filter:search">
				    	<div class="nm-single-cat" ng-click="viewProducts(singlecat.products, singlecat.name)">
					    	<img ng-src="{{singlecat.thumbnail == false ? '<?php echo plugins_url( 'placeholder.jpg', __FILE__ ); ?>' : singlecat.thumbnail}}" />
							<h2>{{singlecat.name}} <?php if ($productCount) { echo '- ({{singlecat.totalproducts}})'; } ?></h2>
							<p ng-show="layout == 'inline'">{{singlecat.desc}}</p>
							<div class="clearfix"></div>
						</div>
				    </div>
				</div>				
			</div>

			<!-- Products -->

			<div ng-switch-when="allproducts">
				<div class="grid grid-pad">
				    <div ng-class="{ 'col-<?php echo $itemsInRow; ?>-12': layout == 'grid' , 'inline': layout == 'inline' }" ng-repeat="product in currentProducts | filter:search">
				    	<div class="nm-single-cat" ng-click="viewSingle(product.id, product.name)">
					    	<span ng-bind-html="printHTML(product.thumbnail)"></span>
							<h2>{{product.name}}</h2>
							<span class="productprice" ng-bind-html="printHTML(product.price)"></span>
							<p ng-show="layout == 'inline'">{{product.excerpt}}</p>
							<div class="clearfix"></div>
						</div> 
				    </div>
				</div>
			</div>

			<!-- Single Products -->

			<div ng-switch-when="singleproduct" class="nm-single-product" ng-bind-html="printHTML(currentProductHTML)">
				
			</div>
			<div class="clearfix"></div>

			<!-- Cart Template -->

			<div ng-switch-when="carttemplate" class="woocommerce-cart nm-cart" ng-bind-html="printHTML(cartTemplateHTML)">
				
			</div>
			<div class="clearfix"></div>

			<!-- Chackout Template -->

			<div ng-switch-when="checkouttemplate" class="woocommerce-checkout nm-checkout" ng-bind-html="printHTML(checkoutTemplateHTML)">
				
			</div>
			<div class="clearfix"></div>

		</div>
	</div>
</div>