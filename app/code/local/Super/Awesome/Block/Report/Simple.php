<?php

class Super_Awesome_Block_Adminhtml_Report_Simple extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'awesome';
        $this->_controller = 'adminhtml_report_simple';
        $this->_headerText = Mage::helper('awesome')->__('Simple Report');
        parent::__construct();
        $this->_removeButton('add');
    }
}