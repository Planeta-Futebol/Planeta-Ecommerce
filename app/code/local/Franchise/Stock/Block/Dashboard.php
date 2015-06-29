<?php
class Franchise_Stock_Block_Dashboard extends Mage_Core_Block_Template
{
  public function __construct() {
    parent::__construct();
    $userid = Mage::getSingleton('customer/session')->getCustomer()->getId();

  }

  protected function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }
}
