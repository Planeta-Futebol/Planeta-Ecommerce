<?php
class Devinc_Ajaxcartx_Model_Observer
{
	//DEPRECATED
	public function getBlockNames($observer) {
		$layout = Mage::app()->getLayout();
        $xml = $layout->getUpdate()->asSimplexml();

		Mage::getModel('license/module')->resetAcBlocks();
		$acBlocks = Mage::getModel('license/module')->getAcBlocks();
		
		$acBlocks = $this->_getBlocks($xml, $acBlocks);

		// print_r($acBlocks);
			
		Mage::getModel('license/module')->encryptAcBlocks($acBlocks);
	}

	//DEPRECATED
	protected function _getBlocks($parent, $_acBlocks) {
		foreach ($parent as $node) {
		    $attributes = $node->attributes();
		    if ((bool)$attributes->ignore) {
		        continue;
		    }	   
	        if ($node->getName()=='block' && isset($node['type']) && isset($node['name'])) {
		        $type = (string)$node['type'];
		        $name = (string)$node['name'];
		        if ($type=='checkout/cart_sidebar' || $type=='catalog/product_compare_sidebar' || $type=='wishlist/customer_sidebar' || $type=='enterprise_wishlist/customer_sidebar') {
		        	if (!isset($_acBlocks[$type])) {
			        	$_acBlocks[$type] = array();
		        	}
		        	if (!in_array($name, $_acBlocks[$type], true)) {
			        	$i = count($_acBlocks[$type]);
			        	$_acBlocks[$type][$i] = $name;      
			        } else {
		        		$i = array_search($name, $_acBlocks[$type]);
			        }
		            $_acBlocks[$type][$i] = $name;
		        } else if (in_array($type, array('checkout/cart','catalog/product_compare_list','wishlist/customer_wishlist'))) {
		        	$_acBlocks[$type] = $name;
		        }
	        }
	        if (count($node)>0) {
	        	$_acBlocks = $this->_getBlocks($node, $_acBlocks);
	        }
		}

		return $_acBlocks;
	}

	public function updateBlocksBefore($observer)
	{		
        $block = $observer->getEvent()->getBlock();

        if ($block->getNameInLayout() == 'root' && $block->getType() == 'page/html' && (Mage::getModel('customer/session')->getPackage()!=Mage::getModel('core/design_package')->getPackageName() || Mage::getModel('customer/session')->getTheme()!=Mage::getModel('core/design_package')->getTheme('default'))) {
        	Mage::getModel('license/module')->resetAcBlocks();
        	$package = Mage::getModel('core/design_package')->getPackageName();
        	Mage::getModel('customer/session')->setPackage($package);
        	$theme = Mage::getModel('core/design_package')->getTheme('default');
        	Mage::getModel('customer/session')->setTheme($theme);
        }
        if ($block->getType() == 'checkout/cart') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('cart');
        }
        if ($block->getType() == 'checkout/cart_sidebar') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('cart-sidebar');
        }
        if ($block->getType() == 'wishlist/customer_sidebar' || $block->getType() == 'enterprise_wishlist/customer_sidebar') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('wishlist-sidebar');
        }
        if ($block->getType() == 'catalog/product_compare_sidebar') {
        	Mage::getSingleton('customer/session')->setAjaxCartAction('compare-sidebar');
        }        
        if ($block->getNameInLayout() == 'head' && Mage::getStoreConfig('ajaxcartx/configuration/enabled') && Mage::getStoreConfig('ajaxcartx/dragdrop/enable_category_dragdrop')) { 
        	if (!Mage::helper('ajaxcartx')->isMobile() && !Mage::helper('ajaxcartx')->isTablet()) {
        		// $block->setIsTablet(true);
        		$block->setAllowDragAndDrop(true);
        	} 
        	// if (Mage::helper('ajaxcartx')->isTablet() && (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS') !== FALSE)) {
        		// $block->setIsTablet(false);
        		// $block->setAllowDragAndDrop(false);
        	// }        	
        }      
	}
	
	public function updateBlocksAfter($observer)
	{		
        $block = $observer->getEvent()->getBlock();
        $_transportObject = $observer->getEvent()->getTransport();
        $html = $_transportObject->getHtml();
        
		$acBlocks = Mage::getModel('license/module')->getAcBlocks();

		if (strpos($block->getNameInLayout(), 'ANONYMOUS') !== false) {
			$blockName = $block->getData('name');
		} else {
			$blockName = $block->getNameInLayout();
		}
		
		if ($html != '' || $block->getType() == 'wishlist/customer_sidebar' || $block->getType() == 'enterprise_wishlist/customer_sidebar') {
	        if ($block->getType() == 'checkout/cart') {        
	        	$acBlocks['checkout/cart'] = $blockName;
	        	
	        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
	        		$html = '<div id="ac-cart">'.$html.'</div>';
					$_transportObject->setHtml($html);
	        	}
	        	
				Mage::getSingleton('customer/session')->setAjaxCartAction();
	        } else if ($block->getType() == 'checkout/cart_sidebar') {
	        	if (!isset($acBlocks['checkout/cart_sidebar'])) {
		        	$acBlocks['checkout/cart_sidebar'] = array();
	        	}
	        	if (!in_array($blockName, $acBlocks['checkout/cart_sidebar'], true)) {
		        	$i = count($acBlocks['checkout/cart_sidebar']);
		        	$acBlocks['checkout/cart_sidebar'][$i] = $blockName;      
		        } else {
			        $i = array_search($blockName, $acBlocks['checkout/cart_sidebar']);
		        }

		        if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
		        	$headerClass = '';
		        	if (($block->getParentBlock() && $block->getParentBlock()->getNameInLayout() == 'header') || ($block->getParentBlock() && $block->getParentBlock()->getParentBlock() && $block->getParentBlock()->getParentBlock()->getNameInLayout() == 'header')) {
		        		$headerClass = ' ac-header';
		        	}
		        	$html = '<div class="ac-cart-sidebar'.$headerClass.'" id="ac-cart-sidebar'.$i.'">'.$html.'</div>';
					$_transportObject->setHtml($html);
		        }
		        
				Mage::getSingleton('customer/session')->setAjaxCartAction();
	        } else if ($block->getType() == 'wishlist/customer_sidebar' || $block->getType() == 'enterprise_wishlist/customer_sidebar') {    
	            if (!isset($acBlocks[$block->getType()])) {
		        	$acBlocks[$block->getType()] = array();
	        	}
	        	if (!in_array($blockName, $acBlocks[$block->getType()], true)) {
		        	$i = count($acBlocks[$block->getType()]);
		        	$acBlocks[$block->getType()][$i] = $blockName;      
		        } else {
			        $i = array_search($blockName, $acBlocks[$block->getType()]);
		        }	    
	        	
	        	if (!Mage::getSingleton('customer/session')->getAjaxRequest() && Mage::helper('customer')->isLoggedIn()) {
	        		$headerClass = '';
		        	if (($block->getParentBlock() && $block->getParentBlock()->getNameInLayout() == 'header') || ($block->getParentBlock() && $block->getParentBlock()->getParentBlock() && $block->getParentBlock()->getParentBlock()->getNameInLayout() == 'header')) {
		        		$headerClass = ' ac-header';
		        	}
	        		$html = '<div class="ac-wishlist-sidebar'.$headerClass.'" id="ac-wishlist-sidebar'.$i.'">'.$html.'</div>';
					$_transportObject->setHtml($html);
	        	}
	        	
				Mage::getSingleton('customer/session')->setAjaxCartAction();
	        } else if ($block->getType() == 'catalog/product_compare_sidebar') { 
	        	if (!isset($acBlocks['catalog/product_compare_sidebar'])) {
		        	$acBlocks['catalog/product_compare_sidebar'] = array();
	        	}
	        	if (!in_array($blockName, $acBlocks['catalog/product_compare_sidebar'], true)) {
		        	$i = count($acBlocks['catalog/product_compare_sidebar']);
		        	$acBlocks['catalog/product_compare_sidebar'][$i] = $blockName;      
		        } else {
			        $i = array_search($blockName, $acBlocks['catalog/product_compare_sidebar']);
		        }	        
	        	
	        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
	        		$headerClass = '';
		        	if (($block->getParentBlock() && $block->getParentBlock()->getNameInLayout() == 'header') || ($block->getParentBlock() && $block->getParentBlock()->getParentBlock() && $block->getParentBlock()->getParentBlock()->getNameInLayout() == 'header')) {
		        		$headerClass = ' ac-header';
		        	}
	        		$html = '<div class="ac-compare-sidebar'.$headerClass.'" id="ac-compare-sidebar'.$i.'">'.$html.'</div>';
					$_transportObject->setHtml($html);
	        	}
	        	
				Mage::getSingleton('customer/session')->setAjaxCartAction();
	        } else if ($block->getType() == 'catalog/product_list' || $block->getType() == 'catalog/product_new' || $block->getType() == 'multipledeals/list' || $block->getType() == 'multipledeals/recent' || $block->getType() == 'groupdeals/product_list' || ($block->getLayer() && strpos($block->getType(), 'layer') === false) || ($block->getType()!='awshopbybrand/brand' && $block->getProductCollection() && strpos($block->getType(), 'list') !== false) || ($block->getLoadedProductCollection() && strpos($block->getType(), 'list') !== false)) {	        
	        	$html = '<div class="ac-product-list">'.$html.'</div>';
	        	$html .= '<script type="text/javascript">if (typeof ajaxcartx != \'undefined\') { ajaxcartx.updateAjaxCartBlocks(true); }</script>';
	        	$_transportObject->setHtml($html);
	        } else if ($block->getType() == 'page/template_links' ) {	        
	        	$html = '<div id="ac-links">'.$html.'</div>';
	        	$_transportObject->setHtml($html);
	        } else if ($block->getType() == 'catalog/product_compare_list' ) {	        
	        	$acBlocks['catalog/product_compare_list'] = $blockName;
	        	
	        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
	        		$html = '<div id="ac-compare-popup">'.$html.'</div>';
					$_transportObject->setHtml($html);
	        	}
	        	
				Mage::getSingleton('customer/session')->setAjaxCartAction();
	        } else if ($block->getType() == 'wishlist/customer_wishlist') {
		        $acBlocks['wishlist/customer_wishlist'] = $blockName;
	        	
	        	if (!Mage::getSingleton('customer/session')->getAjaxRequest()) {
	        		$html = '<div id="ac-wishlist">'.$html.'</div>';
					$_transportObject->setHtml($html);
	        	}
	        	
				Mage::getSingleton('customer/session')->setAjaxCartAction();
	        }
	    }
	    
		Mage::getModel('license/module')->encryptAcBlocks($acBlocks);
	}
	
	//saves the min/max qty's of all the products in every collection in a session
	public function setProductMinMax($observer)
    {
    	$helper = Mage::helper('ajaxcartx');
    	if (Mage::getSingleton('customer/session')->getProductMinMax()) {
    		$productMinMax = unserialize(Mage::getSingleton('customer/session')->getProductMinMax());
    	} else {
			$productMinMax = array();
		}
		
    	$products = $observer->getEvent()->getCollection();	
        Mage::getModel('cataloginventory/stock')->addItemsToProducts($products);	
        
    	foreach ($products as $product) {
			$productMinMax[$product->getId()] = array();
    		$productMinMax[$product->getId()]['min'] = $helper->getMinimumProductQty($product);
    		$productMinMax[$product->getId()]['max'] = $helper->getMaximumProductQty($product);
    		$productMinMax[$product->getId()]['inc'] = $helper->getQtyIncrements($product);

    		if (Mage::helper('wishlist')->isAllow()) {
    			$productMinMax[$product->getId()]['wishlist'] = str_replace('wishlist/index/add/', 'ajaxcartx/wishlist/add/', Mage::helper('wishlist')->getAddUrl($product));
    		}
    		$compareUrl = Mage::helper('catalog/product_compare')->getAddUrl($product);
    		if ($compareUrl) {
    			$productMinMax[$product->getId()]['compare'] = str_replace('catalog/product_compare/add/', 'ajaxcartx/product_compare/add/', $compareUrl);    			
    		}
		}
		
		Mage::getSingleton('customer/session')->setProductMinMax(serialize($productMinMax));
	}
	
	//save the options of the configurable products when updating them on the shopping cart page
	public function updateCustomOptions($observer)
	{
		$_this = $observer->getEvent()->getCart();
		$data = $observer->getEvent()->getInfo();
		
		foreach ($data as $itemId => $itemInfo) {
			$item = $_this->getQuote()->getItemById($itemId);
			if (!$item) continue;
			if (!isset($itemInfo['option']) or empty($itemInfo['option'])) continue;
			
			$confProduct = Mage::getModel('catalog/product')->load($item->getProductId());
			$child = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($itemInfo['option'],$confProduct);

			$options = $item->getOptions();
			foreach ($options as $option){
				if ($option->getCode()=='info_buyRequest'){
					$unserialized = unserialize($option->getValue());
					$unserialized['super_attribute'] = $itemInfo['option'];
					$option->setValue(serialize($unserialized));
				} elseif ($option->getCode()=='attributes'){
					$option->setValue(serialize($itemInfo['option']));
				} elseif (substr($option->getCode(),0,12)=='product_qty_') {		
					$option->setProductId($child->getId());	
					$option->setCode('product_qty_'.$child->getId());									
				} elseif ($option->getCode()=='simple_product') {		
					$option->setProductId($child->getId());	
					$option->setValue($child->getId());									
				}
			}			
			
			$item->setProductOptions($options);
			$item->save();
		}
	}		
}
