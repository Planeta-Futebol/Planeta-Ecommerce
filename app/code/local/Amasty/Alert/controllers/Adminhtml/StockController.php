<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Alert
 */
class Amasty_Alert_Adminhtml_StockController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() 
	{
	    $this->loadLayout(); 
        $this->_setActiveMenu('report/amalert');
        if (!Mage::helper('ambase')->isVersionLessThan(1,4)){
            $this
                ->_title($this->__('Reports'))
                ->_title($this->__('Alerts'))
                ->_title($this->__('Stock Alerts')); 
        }       
        $this->_addBreadcrumb($this->__('Alerts'), $this->__('Stock Alerts')); 
        $this->_addContent($this->getLayout()->createBlock('amalert/adminhtml_stock')); 	    
 	    $this->renderLayout();
	}
}