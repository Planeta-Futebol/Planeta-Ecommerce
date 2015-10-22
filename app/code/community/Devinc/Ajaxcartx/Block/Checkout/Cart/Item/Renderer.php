<?php
class Devinc_Ajaxcartx_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{
    
    //Get quote item qty
    //Add qty input with increase/decrease buttons if enabled 
    public function getQty()
    {
    	if (Mage::helper('core')->isModuleEnabled('Devinc_Ajaxcartx')) {
	    	return Mage::helper('ajaxcartx')->getQty($this, parent::getQty()); 
	    } else {
		    return parent::getQty();
	    }
    }   
    
}