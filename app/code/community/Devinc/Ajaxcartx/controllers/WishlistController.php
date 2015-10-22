<?php
require_once Mage::getModuleDir('controllers', 'Mage_Wishlist').DS.'IndexController.php';
class Devinc_Ajaxcartx_WishlistController extends Mage_Wishlist_IndexController
{	
	//used in addAction(), removeAction() and fromcartAction() functions
	public function norouteAction($coreRoute = NULL) {
		return $this->_refreshWishlistBlocks();
	}
	
	protected function _validateFormKey() {
		return true;
	}

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

	//used in addAction(), cartAction(), updateAction() and updateItemOptionsAction() functions
	protected function _redirect($path, $arguments=array())
	{
		$calledFrom = Mage::helper('ajaxcartx')->getCallingFunction();

		//if the _redirect() function was called from cartAction() it means it had an error, in which case the wishlist blocks will be refreshed
		if ($calledFrom == 'cartAction' || $calledFrom == 'updateAction') {
			return $this->_refreshWishlistBlocks();
		}
			
	    $result = array();
	    
		//update layout messages if not updateItemOptionsAction()
		if ($calledFrom!='updateItemOptionsAction') {			
			$productId = (int) $this->getRequest()->getParam('product', false);
			$product = Mage::getModel('catalog/product')->load($productId);
			
			if (!$productId || !$product->getId() || !$product->isVisibleInCatalog()) {
				$result['error'] = -1;
				$result['message'] = $this->__('Cannot specify product.');
	        } else {
	        	//delete original wishlist success message
	        	$session = Mage::getSingleton('customer/session');		
				$messages = $session->getMessages();
				foreach( $messages->getItems() as $message ){
					if ($message->getType() == 'success'){
						$message->setIdentifier('remove_success_messages');
					}
				}
				$messages->deleteMessageByIdentifier('remove_success_messages');
	        
	        	//add ajaxcartx wishlist success message
				$message = $this->__('%s has been added to your wishlist.', $product->getName());
		        $session->addSuccess($message);
				$result['popup'] = 'success';
				$result['is_action'] = 'wishlist'; 

				$product = $this->_initProduct();
				if (Mage::getStoreConfig('ajaxcartx/popup_configuration/show_product_images') && $product) {
	            	$result['update_section']['html_product_images'] = Mage::helper('ajaxcartx')->getProductImagesHtml($this);
	            }
		    }		
	    } else {
			$result['close_popup'] = 'options';	
		}

		if ($closePopup = $this->getRequest()->getParam('close_popup', false)) {
			$result['close_popup'] = $closePopup;
		}
	        
	    $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();	
	        
	    //load wishlist page html only if on the wishlist page
		if($this->getRequest()->getParam('is_wishlist_page', false)) {
		    $result['update_section']['html_wishlist_page'] = Mage::helper('ajaxcartx')->getWishlistPageHtml($this);
		} else {
		    $result['update_section']['html_wishlist'] = Mage::helper('ajaxcartx')->getWishlistHtml($this);
		}
        $result['update_section']['html_wishlist_link'] = Mage::helper('ajaxcartx')->getWishlistLinkHtml($this);     
		$result['update_section']['html_ajaxcartx_js'] = Mage::getModel('ajaxcartx/ajaxcartx')->getAjaxCartQtysJs();
		
		Mage::getModel('license/module')->runAjax($result, $this);
	}	
	
	public function configureAction() {
		$this->_redirectUrl('/configure/');
	}

	//used in cartAction(), allcartAction() and fromcartAction() functions
	protected function _redirectUrl($_url)
	{
		$calledFrom = Mage::helper('ajaxcartx')->getCallingFunction();
		if(strpos($_url, '/configure/') !== false) {
			if ($calledFrom=='fromcartAction' || $calledFrom=='configureAction') {
		        $itemId = (int) $this->getRequest()->getParam('id');
			} else {
		        $itemId = (int) $this->getRequest()->getParam('item');
		    }
	        $item = Mage::getModel('wishlist/item')->loadWithOptions($itemId);
	        $product = Mage::getModel('catalog/product')->load($item->getProductId());
	        
			$result = array();			
			
			if (!$this->getRequest()->getParam('skip_popup', false)) {
				//open configuration popup
				$result['popup'] = 'options';				
				if ($calledFrom=='configureAction') {
					$result['form_action'] = Mage::helper('wishlist')->getUpdateUrl($item).'product/'.$product->getId().'/';				
				} else {
					$result['form_action'] = Mage::helper('wishlist')->getAddToCartUrl($item);
				}
				
				//set qty
				if ($qty = $this->getRequest()->getParam('qty', false)) {
			        if (is_array($qty)) {
			            if (isset($qty[$itemId])) {
			                $qty = $qty[$itemId];
			            } else {
			                $qty = 1;
			            }
			        }
			        $qty = $this->_processLocalizedQty($qty);
			        if ($qty) {
			            $item->setQty($qty);
			        }
				} 		
				
				$buyRequest = $item->getBuyRequest();
	            if (!$buyRequest->getQty() && $item->getQty()) {
	                $buyRequest->setQty($item->getQty());
	            }
				Mage::helper('catalog/product')->prepareProductOptions($product, $buyRequest);
				
				$result['product_id'] = $product->getId();
				Mage::register('product', $product);
				Mage::register('current_product', $product);
				
	        	$result['update_section']['html_options_layout_messages'] = $this->_getLayoutMessagesHtml();
				if (!$product->isGrouped()){
					if ($product->getTypeId()=='configurable'){
						Mage::getSingleton('customer/session')->setAjaxCartAction('options_popup_conf');
						$result['update_section']['html_options'] = Mage::helper('ajaxcartx')->getConfigurableHtml($this);
						Mage::getSingleton('customer/session')->setAjaxCartAction();
					} else if ($product->getTypeId()=='bundle'){
						$result['update_section']['html_options'] = Mage::helper('ajaxcartx')->getBundleHtml($this);
					} else if ($product->getTypeId()=='downloadable'){
						$result['update_section']['html_options'] = Mage::helper('ajaxcartx')->getDownloadableHtml($this);
					} else if ($product->getTypeId()=='giftcard'){
						$result['update_section']['html_options'] = Mage::helper('ajaxcartx')->getGiftCardHtml($this);
					} else {
						$result['update_section']['html_options'] = Mage::helper('ajaxcartx')->getOptionsHtml($this);				
					}
				} else {
					$result['update_section']['html_options'] = Mage::helper('ajaxcartx')->getGroupedHtml($this);
				}
	        } else {
				//print error if configure popup is already opened and the configure action was re-called 
				$result['error'] = -1;
				//get catalog error messages			
				$session = Mage::getSingleton('catalog/session');			
				$messages = $session->getMessages();
				foreach( $messages->getItems() as $message ){
					if ($message && $message->getType() != 'success'){
				    	$result['message'][] = $message->getText();
					}
				}
				
				//get wishlist error messages	
				$session = Mage::getSingleton('wishlist/session');			
				$messages = $session->getMessages();
				foreach( $messages->getItems() as $message ){
					if ($message && $message->getType() != 'success'){
				    	$result['message'][] = $message->getText();
					}
				}
				
				//load error messages in background to not show up again the second time
				Mage::getSingleton('catalog/session')->getMessages(true);
				Mage::getSingleton('wishlist/session')->getMessages(true);				
			}
		} else {				
	        $result = array();
	        if ($calledFrom!='fromcartAction') {
				$result['popup'] = 'success';
				$result['is_action'] = 'cart'; 
				$result['close_popup'] = 'options';		
				
				//add success message if item was moved to the cart from the wishlist; if it still exists an error will be displayed (i.e. product is out of stock)
		        $itemId = (int) $this->getRequest()->getParam('item');
		        $item = Mage::getModel('wishlist/item')->load($itemId);
		        if ($calledFrom!='allcartAction' && !$item->getId()) {	 
					$session = Mage::getSingleton('checkout/session');		
					$productId = $session->getLastAddedProductId(true);
					$product = Mage::getModel('catalog/product')->load($productId);	

					if (Mage::getStoreConfig('ajaxcartx/popup_configuration/additional_content')!='') {
						Mage::unregister('current_product');
						Mage::register('current_product',$product);				
						Mage::unregister('product');
						Mage::register('product',$product);

		            	$type = Mage::getStoreConfig('ajaxcartx/popup_configuration/additional_content');
		            	$result['update_section']['html_additional_content'] = Mage::helper('ajaxcartx')->getAdditionalContentHtml($this, $type);
					}

					$message = $this->__('%s was added to your shopping cart.', $product->getName());
					$session->addSuccess($message);
				}    			
		        $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();
			}	 
			    
			$result['update_section']['html_wishlist_link'] = Mage::helper('ajaxcartx')->getWishlistLinkHtml($this); 
			
			//load wishlist sidebar html only if not on wishlist page
			if(!$this->getRequest()->getParam('is_wishlist_page', false)) {
			    $result['update_section']['html_wishlist'] = Mage::helper('ajaxcartx')->getWishlistHtml($this);
			} else {
			    $result['update_section']['html_wishlist_page'] = Mage::helper('ajaxcartx')->getWishlistPageHtml($this);
			}
			
			$result['update_section']['html_cart'] = Mage::helper('ajaxcartx')->getCartHtml($this);
			$result['update_section']['html_cart_link'] = Mage::helper('ajaxcartx')->getCartLinkHtml($this);
			
			//load cart page html only if on the shopping cart page; 
			if($this->getRequest()->getParam('is_cart_page', false)) {
				$this->_initLayoutMessages(array('checkout/session', 'wishlist/session', 'catalog/session', 'customer/session'));
			    $result['update_section']['html_cart_page'] = Mage::helper('ajaxcartx')->getCartPageHtml($this);
			    $result['update_section']['html_ajaxcartx_js'] = Mage::getModel('ajaxcartx/ajaxcartx')->getAjaxCartQtysJs();
			} else if($this->getRequest()->getParam('is_wishlist_page', false)) {	            
			    $result['update_section']['html_ajaxcartx_js'] = Mage::getModel('ajaxcartx/ajaxcartx')->getAjaxCartQtysJs();
			}
		}
		
		Mage::getModel('license/module')->runAjax($result, $this);
	}	
	
	//used in removeAction() function
	protected function _redirectReferer($defaultUrl=null)
	{
		return $this->_refreshWishlistBlocks();
	}
	
	protected function _refreshWishlistBlocks() {
		Mage::getSingleton('customer/session')->getMessages(true);
		
        $result = array();
        
        $result['update_section']['html_wishlist_link'] = Mage::helper('ajaxcartx')->getWishlistLinkHtml($this);   
		    
		//load wishlist page html only if on the wishlist page; else load wishlist sidebar
		if($this->getRequest()->getParam('is_wishlist_page', false)) {
		    $result['update_section']['html_wishlist_page'] = Mage::helper('ajaxcartx')->getWishlistPageHtml($this);
			$result['update_section']['html_ajaxcartx_js'] = Mage::getModel('ajaxcartx/ajaxcartx')->getAjaxCartQtysJs();
		} else {
		    $result['update_section']['html_wishlist'] = Mage::helper('ajaxcartx')->getWishlistHtml($this);			
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
			$this->_initLayoutMessages(array('checkout/session', 'wishlist/session', 'catalog/session', 'customer/session'));
		}
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_messages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
}