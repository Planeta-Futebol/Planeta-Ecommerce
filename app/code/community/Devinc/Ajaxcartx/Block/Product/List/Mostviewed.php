<?php
class Devinc_Ajaxcartx_Block_Product_List_Mostviewed extends Mage_Catalog_Block_Product_New
{    
    protected function _construct()
    {
        // $this->_productsCount = 2; 
        parent::_construct();
    }

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
                array('aggregation' => $collection->getResource()->getTable('reports/viewed_aggregated_monthly')),
                "e.entity_id = aggregation.product_id AND aggregation.store_id={$storeId}",
                array()
            )
            ->group('e.entity_id')
            ->order(array('views_num DESC', 'e.created_at'));
            
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
        $url = str_replace('wishlist/index/add/','ajaxcartx/wishlist/add/', $this->helper('wishlist')->getAddUrl($_product));

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