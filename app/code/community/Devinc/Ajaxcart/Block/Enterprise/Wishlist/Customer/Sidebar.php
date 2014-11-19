<?php 
class Devinc_Ajaxcart_Block_Enterprise_Wishlist_Customer_Sidebar extends Enterprise_Wishlist_Block_Customer_Sidebar
{    
	//Prepare before to html          
	//If customer is logged in, display wishlist even if no items are available
	protected function _toHtml()    
	{        
		if (Mage::helper('ajaxcart')->isEnabled() && Mage::getSingleton('customer/session')->isLoggedIn()) {		
			return Mage_Wishlist_Block_Abstract::_toHtml();    
		} else {
			return parent::_toHtml();
		}
	}	
}