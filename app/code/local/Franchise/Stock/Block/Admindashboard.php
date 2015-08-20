<?php
  class Franchise_Stock_Block_Admindashboard extends Mage_Core_Block_Template {
    public function __construct() {
      parent::__construct();
      $post = $this->getRequest()->getPost();
      $collection = Mage::getModel("stock/Saleperpartner")->getCollection()
      ->setOrder('sale_at');
      if (!empty($post) and $post['franchise']!=0) {
        $collection->addFieldToFilter('userid', array('eq'=>$post['franchise']));
      }
      $this->setCollection($collection);
      //$this->setTemplate('stock/dashboard.phtml');
    }

    //protected function _prepareLayout() {
    //  parent::_prepareLayout();
    //}

    protected function _prepareLayout() {
      parent::_prepareLayout();
      $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
      $pager->setAvailableLimit(array(10=>10, 20=>20, 30=>30, 'all'=>'all'));
      $pager->setCollection($this->getCollection());
      $this->setChild('pager', $pager);
      $this->getCollection()->load();
      return $this;
    }

    //public function preparePager() {
    //  $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
    //  //$pager->setAvailableLimit(array(10=>10, 20=>20, 30=>30, 'all'=>'all'));
    //  $pager->setAvailableLimit(array(2=>2, 3=>3, 5=>5, 'all'=>'all'));
    //  $pager->setCollection($this->getCollection());
    //  $this->setChild('pager', $pager);
    //  $this->getCollection()->load();
    //  return $this;
    //}

    //public function getPagerHtml() {
    //  return $this->getChildHtml('pager');
    //}

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
       return 6;
    }
  }
?>
