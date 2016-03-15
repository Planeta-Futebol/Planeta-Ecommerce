<?php
require_once Mage::getModuleDir('controllers', 'Mage_Catalog').DS.'Product'.DS.'CompareController.php';
class Devinc_Ajaxcartx_Product_CompareController extends Mage_Catalog_Product_CompareController
{	
	protected function _initProduct()    
	{
		$productId = $this->getRequest()->getParam('product',false); 
		if ($productId) {            
			$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);            
			if ($product->getId()) {
				Mage::unregister('current_product');
				Mage::register('current_product',$product);				
				Mage::unregister('product');
				Mage::register('product',$product);
				return $product;           
			}        
		}       
		return false;    
	}

	protected function _redirectReferer($defaultUrl = NULL)
	{
		$action = Mage::app()->getFrontController()->getRequest()->getActionName();
		
		if ($action == 'remove' || $action == 'clear'){
			// Load messages in background in case of errors
			Mage::getSingleton('catalog/session')->getMessages(true);		
		} 
		
        $result = array();

        if (!is_array(Mage::getModel('license/module')->getAcBlocks()) && $this->getRequest()->getParam('redirect_url', false)) {
            Mage::getSingleton('checkout/session')->addError($this->__('Your session has expired.'));
			$result['redirect'] = $this->getRequest()->getParam('redirect_url');
			
			Mage::getModel('license/module')->runAjax($result, $this);
        	return;
        }

		if ($action != 'remove' && $action != 'clear'){
			$result['popup'] = 'success';
			if ($closePopup = $this->getRequest()->getParam('close_popup', false)) {
				$result['close_popup'] = $closePopup;
			}
			$result['is_action'] = 'compare';	
			$result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();
			$result['update_section']['compare_onclick'] = "popWin('".Mage::helper('catalog/product_compare')->getListUrl()."', 'compare', 'top:0, left:0, width=820, height=600, resizable=yes, scrollbars=yes');ajaxcartxTools.hidePopup('success', true);";

			$product = $this->_initProduct();
            if (Mage::getStoreConfig('ajaxcartx/popup_configuration/show_product_images') && $product) {
            	$result['update_section']['html_product_images'] = Mage::helper('ajaxcartx')->getProductImagesHtml($this);
            }
		}   
		
		$result['update_section']['html_compare'] = Mage::helper('ajaxcartx')->getCompareHtml($this);
		
		//update compare popup
		if ($this->getRequest()->getParam('is_compare_popup', false)) {
			$result['update_section']['html_compare_popup'] = Mage::helper('ajaxcartx')->getComparePopupHtml($this);
		}
		
		Mage::getModel('license/module')->runAjax($result, $this);
	}
	
    protected function _getLayoutMessagesHtml()
    {        
	    $cache = Mage::app()->getCacheInstance();
		$cache->banUse('layout');
		
		/* Mage::getModel('ajaxcartx/ajaxcartx')->loadQuoteMessages(); */
        $layout = $this->getLayout();   
        if (Mage::helper('ajaxcartx')->getMagentoVersion()>1411) {     
			$this->_initLayoutMessages(array('checkout/session', 'catalog/session', 'customer/session'));
		}
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_messages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
	
}