<?php
  class Franchise_Stock_Block_Admindashboard extends Mage_Core_Block_Template {
    public function __construct() {
      parent::__construct();
      $this->setTemplate('stock/dashboard.phtml');
    }

    protected function _prepareLayout() {
      parent::_prepareLayout();
    }

    public function preparePager() {
      $pager = $this->getLayout()->createBlock('page/html_pager', 'admindashboard.pager');
      $pager->setCollection($this->getCollection());
      $this->setChild('pager', $pager);
      $this->getCollection()->load();

      return $this;
    }

    public function getPagerHtml() {
      return $this->getChildHtml('pager');
    }
  }
?>
