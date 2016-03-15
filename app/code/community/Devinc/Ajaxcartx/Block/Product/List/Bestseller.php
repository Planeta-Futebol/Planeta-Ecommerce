<?php
class Devinc_Ajaxcartx_Block_Product_List_Bestseller extends Mage_Catalog_Block_Product_Abstract
{    
    public function getAdditionalContentType() {
    	return Mage::getStoreConfig('ajaxcartx/popup_configuration/additional_content');
    }

    public function getAdditionalContent() {
        $storeId = (int) Mage::app()->getStore()->getId();
        $productNumber = Mage::getStoreConfig('ajaxcartx/popup_configuration/product_number');
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addStoreFilter()
            ->addPriceData()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->setPageSize($productNumber);
        $collection->getSelect()
            ->joinLeft(
                array('aggregation' => $collection->getResource()->getTable('sales/bestsellers_aggregated_monthly')),
                "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId}",
                array('SUM(aggregation.qty_ordered) AS sold_quantity')
            )
            ->group('e.entity_id')
            ->order(array('sold_quantity DESC', 'e.created_at'));
            
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        return $collection;
    }

    public function getAcAddToCartUrl($_product) {      
        $params = Mage::helper('ajaxcartx')->getUrlParams($this->getAddToCartUrl($_product)); 
        unset($params['uenc']);
        $url = Mage::getUrl('ajaxcartx/index/init', $params);

        return "ajaxcartx.initAjaxcartx('".$url."', this, 'success');";
    }

    public function getAcAddToWishlistUrl($_product) {        
        $url = str_replace('wishlist/index/add/','ajaxcartx/wishlist/add/', Mage::helper('wishlist')->getAddUrl($_product));

        if (Mage::helper('customer')->isLoggedIn()) {
            return "javascript:ajaxcartx.addToWishlist('".$url."', 'success')";
        } else {
            return "javascript:ajaxcartxLogin.loadLoginPopup('".$url."', 'success');";
        }
    }

	public function getAcAddToCompareUrl($_compareUrl) {		
        $url = str_replace('catalog/product_compare/add', 'ajaxcartx/product_compare/add', $_compareUrl);

		return "javascript:ajaxcartx.addToCompare('".$url."', 'success')";
	}

}