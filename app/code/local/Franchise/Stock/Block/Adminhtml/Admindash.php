<?php
class Franchise_Stock_Block_Adminhtml_Admindash extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct(){
    $this->_controller = 'adminhtml_admindash';
    $this->_headerText = Mage::helper('stock')->__("Admindash");
    $this->_blockGroup = 'stock';
    parent::__construct();
    $this->_removeButton('add');
    $this->_removeButton('reset_filter_button');
    $this->_removeButton('search_button');   
  }
}
