/* Setting up Loader */
jQuery('.nm-loader').hide();
jQuery('.woocommerce-message').hide();
var wrapper_width = jQuery('.nm-woostore').width();
jQuery('.nm-loader').css({
    width: wrapper_width,
    height: '100%'
});
jQuery('#reviews').hide();

/**
* nmWoostore Module
*
* Description
*/

angular.module('nmWoostore', [], function($httpProvider) {
          // Use x-www-form-urlencoded Content-Type
          
          $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
          
           /**
           * The workhorse; converts an object to x-www-form-urlencoded serialization.
           * @param {Object} obj
           * @return {String}
           */ 
          var param = function(obj) {
            var query = '', name, value, fullSubName, subName, subValue, innerObj, i;
        
            for(name in obj) {
              value = obj[name];
        
              if(value instanceof Array) {
                for(i=0; i<value.length; ++i) {
                  subValue = value[i];
                  fullSubName = name + '[' + i + ']';
                  innerObj = {};
                  innerObj[fullSubName] = subValue;
                  query += param(innerObj) + '&';
                }
              }
              else if(value instanceof Object) {
                for(subName in value) {
                  subValue = value[subName];
                  fullSubName = name + '[' + subName + ']';
                  innerObj = {};
                  innerObj[fullSubName] = subValue;
                  query += param(innerObj) + '&';
                }
              }
              else if(value !== undefined && value !== null)
                query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
            }
        
            return query.length ? query.substr(0, query.length - 1) : query;
          };
        
          // Override $http service's default transformRequest
          $httpProvider.defaults.transformRequest = [function(data) {
            return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
          }];
}).controller('wooCtrl', function($scope, $http, $sce){

    $scope.allcats = woostore.cats;
    $scope.activeTab = 'categories';
    $scope.layout = 'grid';
    $scope.currentProducts = '';
    $scope.currentProduct = '';
    $scope.currentProductHTML = '';
    $scope.currentCategory = '';
    $scope.currentCartTotal = '';
    $scope.cartTemplateHTML = '';
    $scope.checkoutTemplateHTML = '';

    $scope.viewProducts = function(all_products, category){
        $scope.activeTab = 'allproducts';
        $scope.currentProducts = all_products;
        $scope.currentCategory = category;
    }

    $scope.viewSingle = function(product_id, product_name){
        jQuery('.nm-loader').show();
        $scope.currentProduct = product_name;
        $http.post(woostore.ajaxurl, {action: 'nm_opw_get_single_product'  , product_id: product_id}).
            success(function(resp) {
                $scope.currentProductHTML = resp;
                $scope.activeTab = 'singleproduct';
                jQuery.getScript(woostore.tabscript);
                jQuery.getScript(woostore.variation);
                jQuery.getScript(woostore.pretty_photo);
                setTimeout(function() {
                    jQuery('.nm-single-product .type-product').addClass('product');
                    jQuery('.nm-single-product .related.products').hide();
                    preventingDefaults(product_id);
                }, 100);
            jQuery('.nm-loader').hide();
        });
    }

    $scope.loadCartContents = function(){
        jQuery('.nm-loader').show();
        $http.post(woostore.ajaxurl, {action: 'nm_opw_get_cart_template' }).
            success(function(resp) {
                $scope.cartTemplateHTML = resp;
                $scope.activeTab = 'carttemplate';
                jQuery('.nm-loader').hide();
                setTimeout(function() { preventingDefaults(); }, 100);
        });
    }

    $scope.emptyCart = function(){
        jQuery('.nm-loader').show();
        $http.post(woostore.ajaxurl, {action: 'nm_opw_empty_cart' }).
            success(function(resp) {
                $scope.currentCartTotal = '<span class="totalprice">0 items - <span class="amount">0.00</span></span>';
                $scope.cartTemplateHTML = resp;
                $scope.activeTab = 'carttemplate';
                jQuery('.nm-loader').hide();
                setTimeout(function() { preventingDefaults(); }, 100);
        });
    }


    $scope.printHTML = function(rawHTML){
        return $sce.trustAsHtml(rawHTML);
    }

    $scope.loadCheckoutPage = function(){
        jQuery('.nm-loader').show();
        $http.post(woostore.ajaxurl, {action: 'nm_opw_get_checkout_template' }).
            success(function(resp) {
                $scope.checkoutTemplateHTML = resp;
                $scope.activeTab = 'checkouttemplate';
                jQuery.getScript(woostore.checkout);
                jQuery('.nm-loader').hide();
                setTimeout(function() { preventingDefaults(); jQuery('.woocommerce-info').hide(); }, 100);
        });        
    }

    // console.log($scope.allcats);

    function preventingDefaults(product_id){
        jQuery('.woocommerce-shipping-calculator').hide();


        jQuery('.nm-checkout #billing_country, .nm-checkout #shipping_country').select2();
        jQuery('.nm-single-product form.cart').submit(function(event) {
            event.preventDefault();
            jQuery('.nm-loader').show();
            var formData = jQuery( this ).serialize();
            var the_variations = {};
            jQuery('select[name^="attribute_pa"]').each(function(i, item){
                the_variations[jQuery(item).attr('name')] = jQuery(item).val();
            });

            formData = formData + '&variation=' + JSON.stringify(the_variations) + '&product_id=' + product_id;
            jQuery.post(woostore.ajaxurl, {action: 'nm_opw_add_to_cart', formData}, function(resp) {
                $scope.$apply(function () {
                    $scope.currentCartTotal = resp;
                    $scope.activeTab = 'allproducts';
                });
                jQuery('.nm-loader').hide();
                jQuery('.woocommerce-message').text('"'+$scope.currentProduct+'" was successfully added to your cart.');
                jQuery('.woocommerce-message').slideDown('slow');
                setTimeout(function() { jQuery('.woocommerce-message').slideUp('slow'); }, 2000);
            });
            
        });

        jQuery('.nm-cart .product-remove a.remove').on('click', function(event) {
            event.preventDefault();
            jQuery('.nm-loader').show();
            var thisitem = jQuery(this).closest('tr');
            var itemName = thisitem.find('.product-name').text();
            var link = jQuery(this).attr('href');
            key = link.substring(link.indexOf("&"),link.indexOf("=")+1);
            jQuery.post(woostore.ajaxurl, {action: 'nm_opw_delete_item_from_cart', key: key}, function(resp) {
                $scope.$apply(function () {
                    $scope.currentCartTotal = resp;
                });
                $scope.loadCartContents();
                jQuery('.nm-loader').hide();
                jQuery('.woocommerce-message').text(itemName +' removed.');
                jQuery('.woocommerce-message').slideDown('slow');
                setTimeout(function() { jQuery('.woocommerce-message').slideUp('slow'); }, 2000);
            });
        });

        jQuery('.nmreturn').click(function() {
             $scope.$apply(function () {
                $scope.activeTab = 'categories';
            });
        });

        jQuery('.nm-cart form').submit(function(event) {
            event.preventDefault();
            var coupon = jQuery(this).find('#coupon_code').val();

            if (coupon != '') {
                jQuery('.nm-loader').show();
                $http.post(woostore.ajaxurl, {action: 'nm_opw_apply_coupon_code', code: coupon }).
                    success(function(resp) {
                        $scope.cartTemplateHTML = resp;
                        jQuery('.nm-loader').hide();
                        setTimeout(function() { preventingDefaults(); }, 100);
                        setTimeout(function() {
                            jQuery('.woocommerce-message').slideUp('slow');
                            jQuery('.woocommerce-error').slideUp('slow');
                        }, 2000);
                });
            } else { alert('Coupon Field is Empty!'); }
        });


        jQuery('.nm-cart .quantity input').attr('disabled', 'disabled');
        jQuery('.nm-cart input[name="update_cart"]').hide();
        updating_shipping();

        // jQuery('.nm-single-product .zoom').attr('href', '#');
        jQuery('.nm-single-product .posted_in a, .nm-cart .product-name a').each(function(index, el) {
            var thisText = jQuery(this).text();
            jQuery(this).replaceWith(thisText);
        });

    }

    function updating_shipping(){
        jQuery('#shipping_method').on("change", "select.shipping_method, input[name^=shipping_method]", function() {
            var wc_cart_params = {
                "ajax_url": woostore.ajaxurl,
                "update_shipping_method_nonce": woostore.update_shipping_method_nonce
            };            
            var b = [];
            jQuery("select.shipping_method, input[name^=shipping_method][type=radio]:checked, input[name^=shipping_method][type=hidden]").each(function() {
                b[jQuery(this).data("index")] = jQuery(this).val()
            }), jQuery("div.cart_totals").block({
                message: null,
                overlayCSS: {
                    background: "#fff",
                    opacity: 0.6
                }
            });
            var c = {
                action: "woocommerce_update_shipping_method",
                security: wc_cart_params.update_shipping_method_nonce,
                shipping_method: b
            };
            jQuery.post(wc_cart_params.ajax_url, c, function(b) {
                jQuery("div.cart_totals").replaceWith(b), jQuery("body").trigger("updated_shipping_method");
                jQuery('.woocommerce-shipping-calculator').hide();
                updating_shipping();
            })
        });

        jQuery('.nm-cart .woocommerce-remove-coupon').click(function(event) {
            event.preventDefault();
            var removecoupon = jQuery(this).data('coupon');
            jQuery('.nm-loader').show();
            $http.post(woostore.ajaxurl, {action: 'nm_opw_remove_coupon_code', code: removecoupon }).
                success(function(resp) {
                    $scope.cartTemplateHTML = resp;
                    jQuery('.nm-loader').hide();
                    setTimeout(function() { preventingDefaults(); }, 100);
                    setTimeout(function() {
                        jQuery('.woocommerce-message').slideUp('slow');
                        jQuery('.woocommerce-error').slideUp('slow');
                    }, 2000);
            });
        }); 

        jQuery('.nm-cart .checkout-button').click(function(event) {
            event.preventDefault();
            jQuery('.nm-loader').show();
            $http.post(woostore.ajaxurl, {action: 'nm_opw_get_checkout_template' }).
                success(function(resp) {
                    $scope.checkoutTemplateHTML = resp;
                    $scope.activeTab = 'checkouttemplate';
                    jQuery.getScript(woostore.checkout);
                    jQuery('.nm-loader').hide();
                    setTimeout(function() { preventingDefaults(); jQuery('.woocommerce-info').hide(); }, 100);
            });            

        });

    }
});
