<?php
class Franchise_Stock_Block_Commission extends Mage_Catalog_Block_Product_Abstract
{
  public function __construct() {
    parent::__construct();
    $customerData = Mage::getSingleton('customer/session')->getCustomer();
    $existedAccount = Mage::getModel('affiliateplus/account')->loadByCustomerId($customerData->getId());
    $accountid = $existedAccount->getId();
    $transactionInfo = Mage::getModel('affiliateplus/transaction')->getCollection();
    $transactionInfo->addFieldToFilter('account_id', array('in' => $accountid));
    $this->setCollection($transactionInfo);
  }

  protected function _prepareLayout() {
    parent::_prepareLayout();
    $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');

    $pager->setAvailableLimit(array(10=>10, 20=>20, 30=>30, 'all'=>'all'));
    $pager->setCollection($this->getCollection());

    $this->setChild('pager', $pager);
    $this->getCollection()->load();
    return $this;
  }

  public function getDefaultDirection() {
    return 'asc';
  }

  public function getAvailableOrders() {
    return array('price'=>'Price','name'=>'Name');
  }

  public function getSortBy(){
    return 'collection_id';
  }

  public function getToolbarBlock() {
    $block = $this->getLayout()->createBlock('stock/toolbar', microtime());
    return $block;
  }

  public function getMode() {
      return $this->getChild('toolbar')->getCurrentMode();
  }

  public function getToolbarHtml() {
      return $this->getChildHtml('toolbar');
  }

  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }

  public function getColumnCount() {
     return 4;
  }
}
