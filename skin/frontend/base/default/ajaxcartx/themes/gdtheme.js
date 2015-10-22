cartLink = jQueryAC('#cart_id').find('div:first');
wishlistLink = jQueryAC('#wishlist_id').find('div:first');
							
function dispatchButtonUpdates(button, onClick) {
    if ( onClick.indexOf("wishlist/index/cart") != -1 ) {
        var newLink = onClick.replace("wishlist/index/cart/","ajaxcartx/wishlist/cart/").replace("setLocation('","").replace("')","");
        button.setAttribute("onclick","javascript:ajaxcartx.addWishlistItemToCart('"+newLink+"', false);");
    } 
}

if (jQueryAC('.groupdeals-product-list').length > 0) { 
	ajaxcartx.dragdropCategory = false; 
}