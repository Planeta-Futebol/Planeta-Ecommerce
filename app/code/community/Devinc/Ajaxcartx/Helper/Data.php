<?php

class Devinc_Ajaxcartx_Helper_Data extends Mage_Core_Helper_Abstract
{
    //check if extension is enabled
    public static function isEnabled()
    {
        $storeId = Mage::app()->getStore()->getId();
        $isModuleEnabled = Mage::getStoreConfig('advanced/modules_disable_output/Devinc_Ajaxcartx');
        $isEnabled = Mage::getStoreConfig('ajaxcartx/configuration/enabled', $storeId);
        return ($isModuleEnabled == 0 && $isEnabled == 1);
    }
    
    //returns the ajaxcartx initialize url with all the add to cart parameters
    public function getInitUrl()
    {
        return Mage::getModel('license/module')->getInitUrl();
    }       
    
    public function getUpdateCartUrl()
    {
        $url = Mage::getUrl('ajaxcartx/cart/updatePost', array('form_key'=>Mage::getSingleton('core/session')->getFormKey()));
        if (!Mage::app()->getStore()->isCurrentlySecure()) {
            $url = str_replace('https','http',$url);
        }

        return $url;
    } 
    
    public function getClearCartUrl()
    {
        $url = Mage::getUrl('ajaxcartx/cart/clearCart');
        if (!Mage::app()->getStore()->isCurrentlySecure()) {
            $url = str_replace('https','http',$url);
        }

        return $url;
    }  
    
    //adds qty inputs to cart sidebar items with increase/decrease buttons
    public function getQty($_block, $_qty) 
    {          
        if($this->isEnabled() && Mage::getSingleton('customer/session')->getAjaxCartAction()=='cart-sidebar'){
            if (Mage::getStoreConfig('ajaxcartx/qty_configuration/show_qty_in_cartsidebar')) {
                $itemId = $_block->getItem()->getId();
                return '<ac-qty>'.$itemId.'-'.$_qty.'</ac-qty>';
            } else {
                $itemId = $_block->getItem()->getId();
                return $_qty.'<input value="1" name="cart['.$itemId.'][qty]" class="input-text qty" type="hidden" id="sidebar-qty-'.$itemId.'">';
            }
        } else {
            return $_qty;
        }
    }       
    
    //returns the parameters of a url in array format
    public function getUrlParams($_url)
    {
        return Mage::getModel('license/module')->getUrlParams($_url);
        
        return $params;
    }
    
    public function getCallingFunction()
    {
        return Mage::getModel('license/module')->getCallingFunction();
    }

    public function getMagentoVersion() {
        return Mage::getModel('license/module')->getMagentoVersion();
    }

    /**
     * returns true if the edition of Magento is Enterprise
     * @return boolean
     */
    public function isMagentoEnterprise() {
        return Mage::getConfig ()->getModuleConfig ('Enterprise_Enterprise') && Mage::getConfig ()->getModuleConfig ('Enterprise_AdminGws') && Mage::getConfig ()->getModuleConfig ('Enterprise_Checkout') && Mage::getConfig ()->getModuleConfig ('Enterprise_Customer');
    }
    
    public function hex2rgb($hex) {
        return Mage::getModel('license/module')->hex2rgb($hex);
    }
    
    public function isMobile()
    {           
        if (Mage::getModel('license/module')->isMobile()) {
            return true;
        }
        
        return false;
    }    
    
    public function isTablet()
    {           
        if (Mage::getModel('license/module')->isTablet()) {
            return true;
        }
        
        return false;
    }       
    
    public function getMaximumProductQty($_product)
    {
        $maxQty = Mage::getStoreConfig('cataloginventory/item_options/max_sale_qty'); 
        if ($_product->getTypeId() != 'configurable' && $_product->getTypeId() != 'bundle' && $_product->getTypeId() != 'grouped') {
            $maxQty = number_format(Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty(),0,'',''); 
        }       
        
        $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);

        if (!$stock->getManageStock() || $stock->getBackorders()!=0){
            $maxQty = 9999999999;   
        }   

        /* $maxQty = $maxQty - $this->getCartQty($_product); */
            
        return $maxQty;
    }
    
    public function getMinimumProductQty($_product)
    {
        $_viewBlock = new Mage_Catalog_Block_Product_View;
        $minQty = $_viewBlock->getProductDefaultQty($_product); 
        if ($_viewBlock->getProductDefaultQty($_product) == '') {
            $minQty = 1; 
        }   
        if (Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getEnableQtyIncrements()) {
            $qtyInc = $this->getQtyIncrements($_product);   
            if ($qtyInc>$minQty) {
                $minQty = $qtyInc;
            } else if ($minQty>$qtyInc && $minQty%$qtyInc!=0) {
                $minQty = $qtyInc * (floor($minQty/$qtyInc)+1);             
            }
        }
        
        return $minQty;
    }
    
    public function getQtyIncrements($_product)
    {   
        if (Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getEnableQtyIncrements()) {
            if (Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQtyIncrements() == 0) {
                $qtyIncrements = 1; 
            } else {
                $qtyIncrements = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQtyIncrements();   
            }
        } else {
            $qtyIncrements = 1;
        }
        
        return $qtyIncrements;
    }
    
    protected function getBlockNameByType($type) 
    {
        return Mage::getModel('license/module')->getBlockNameByType($type);
    }

    //generate block html functions 
    public function getLoginHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');
        
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_login');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }
    
    public function getLoginLinkHtml($_controller)
    {        
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {    
            $text = $this->__('Log In');        
        } else {
            $text = $this->__('Log Out');           
        }
        
        return $text;
    }   
        
    public function getCartHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->addUpdate('<remove name="messages"/>');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        
        $output = Mage::getModel('license/module')->generateCartOutput($layout);
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }
    
    public function getCartLinkHtml($_controller)
    {
        $count = Mage::helper('checkout/cart')->getSummaryCount();
        if ($count == 1) {
            $text = $this->__('My Cart (%s item)', $count);
        } elseif ($count > 0) {
            $text = $this->__('My Cart (%s items)', $count);
        } else {
            $text = $this->__('My Cart');
        }
        
        return $text;
    }

    public function getCartPageHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('checkout_cart_index');        
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getBlock($this->getBlockNameByType('checkout/cart'))->toHtml();
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }       
    
    public function getOptionsHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_options');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }   
    
    public function getConfigurableHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_configurable');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }
    
    public function getBundleHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_bundle');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }      

    public function getDownloadableHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_downloadable');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }   
    
    public function getGroupedHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_grouped');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }   

    public function getGiftCardHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_index_giftcard');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }   

    public function getProductImagesHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->load('ajaxcartx_product_images');      
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }   

    public function getAdditionalContentHtml($_controller, $type)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        if ($type=='upsell') {
            $update->load('ajaxcartx_index_upsell');
        } else if ($type=='related') {
            $update->load('ajaxcartx_index_related');            
        } else if ($type=='crosssell') {
            $update->load('ajaxcartx_index_crosssell');          
        } else if ($type=='new') {
            $update->load('ajaxcartx_index_new');            
        } else if ($type=='bestseller') {
            $update->load('ajaxcartx_index_bestseller');         
        } else if ($type=='mostviewed') {
            $update->load('ajaxcartx_index_mostviewed');         
        } else {
            $update->load('ajaxcartx_index_staticblock');      
        }
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }   
    
    public function getWishlistHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        
        $output = Mage::getModel('license/module')->generateWishlistOutput($layout);
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }

    public function getWishlistPageHtml($_controller)
    {
        Mage::helper('wishlist')->getWishlist()->getItemCollection()->clear();
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');        
        $update->addHandle('ajaxcartx_wishlist_index');        
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getBlock($this->getBlockNameByType('wishlist/customer_wishlist'))->toHtml();
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }   
    
    public function getWishlistLinkHtml($_controller)
    {
        $count = Mage::helper('wishlist')->getItemCount();
        if ($count == 1) {
            $text = $this->__('My Wishlist (%s item)', $count);
        } elseif ($count > 0) {
            $text = $this->__('My Wishlist (%s items)', $count);
        } else {
            $text = $this->__('My Wishlist');
        }
        
        return $text;
    }

    public function getCompareHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('default');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        
        $output = Mage::getModel('license/module')->generateCompareOutput($layout);
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }   

    public function getComparePopupHtml($_controller)
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('layout');

        Mage::getSingleton('customer/session')->setAjaxRequest(true);
        $layout = $_controller->getLayout();
        $update = $layout->getUpdate();
        $update->addHandle('ajaxcartx_compare_popup');
        $update->load();
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getBlock($this->getBlockNameByType('catalog/product_compare_list'))->toHtml();
        Mage::getSingleton('customer/session')->setAjaxRequest();
        
        return $output;
    }   
}