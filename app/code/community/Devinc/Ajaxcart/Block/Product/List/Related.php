<?php
class Devinc_Ajaxcart_Block_Product_List_Related extends Mage_Catalog_Block_Product_List_Related
{   
    public function getAdditionalContentType() {
    	return Mage::getStoreConfig('ajaxcart/popup_configuration/additional_content');
    }

    public function getAdditionalContent() {
    	$productNumber = Mage::getStoreConfig('ajaxcart/popup_configuration/product_number');
    	$collection = $this->getItems();
    	$i = 1;
    	foreach ($collection as $key => $product) {
    		if ($i>$productNumber) {
            	$collection->removeItemByKey($key);
        	}
        	$i++;
        }

    	return $collection;
    }

    public function getAcAddToCartUrl($_product) {      
        $params = Mage::helper('ajaxcart')->getUrlParams($this->getAddToCartUrl($_product)); 
        unset($params['uenc']);
        $url = Mage::getUrl('ajaxcart/index/init', $params);

        return "ajaxcart.initAjaxcart('".$url."', this, 'success');";
    }

    public function getAcAddToWishlistUrl($_product) {        
        $url = str_replace('wishlist/index/add/','ajaxcart/wishlist/add/', $this->helper('wishlist')->getAddUrl($_product));

        if (Mage::helper('customer')->isLoggedIn()) {
            return "javascript:ajaxcart.addToWishlist('".$url."', 'success')";
        } else {
            return "javascript:ajaxcartLogin.loadLoginPopup('".$url."', 'success');";
        }
    }

    public function getAcAddToCompareUrl($_compareUrl) {        
        $url = str_replace('catalog/product_compare/add', 'ajaxcart/product_compare/add', $_compareUrl);

        return "javascript:ajaxcart.addToCompare('".$url."', 'success')";
    }

}