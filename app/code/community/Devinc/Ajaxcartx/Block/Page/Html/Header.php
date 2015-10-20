<?php
class Devinc_Ajaxcartx_Block_Page_Html_Header extends Mage_Page_Block_Html_Header
{
    public function getWelcome()
    {
		if (Mage::helper('ajaxcartx')->isEnabled()) {		
			return '<span id="ac-welcome-message">'.parent::getWelcome().'</span>';    
		} else {
			return parent::getWelcome();
		}
    }
    
}