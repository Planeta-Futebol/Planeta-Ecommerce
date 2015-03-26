<?php
class Franchise_Stock_Block_Collection  extends Mage_Catalog_Block_Product_Abstract
{
  //protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
  public function __construct() {
    parent::__construct();

    if(array_key_exists('c', $_GET)) {
      $cate = Mage::getModel('catalog/category')->load($_GET["c"]);
    }

    $userid = Mage::getSingleton('customer/session')->getCustomer()->getId();
    $querydata = Mage::getModel('stock/product')->getCollection()
        ->addFieldToFilter('userid', array('eq' => $userid))
        ->addFieldToFilter('status', array('neq' => 2))
        ->setOrder('stockproductid');

    $rowdata=array();
    foreach ($querydata as  $value) {
      $qty = (int)Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($value->getStockproductid())->getQty();
      if($qty) {
        $rowdata[] = $value->getStockproductid();
      }
    }

    $collection = Mage::getModel('catalog/product')->getCollection();
    $collection->addAttributeToSelect('*');
    //print_r($collection); die;

    if(array_key_exists('c', $_GET)) {
      $collection->addCategoryFilter($cate);
    }

    $collection->addAttributeToFilter('entity_id', array('in' => $rowdata));
    $this->setCollection($collection);
  }

// protected function _prepareLayout() {
//        parent::_prepareLayout();
//        $toolbar = $this->getToolbarBlock();
//        $collection = $this->getCollection();
//
//        if ($orders = $this->getAvailableOrders()) {
//           $toolbar->setAvailableOrders($orders);
//        }
//        if ($sort = $this->getSortBy()) {
//            $toolbar->setDefaultOrder($sort);
//        }
//        if ($dir = $this->getDefaultDirection()) {
//            $toolbar->setDefaultDirection($dir);
//        }
//        $toolbar->setCollection($collection);
// 
//        $this->setChild('toolbar', $toolbar);
//        $this->getCollection()->load();
//    $partner=$this->getProfileDetail();
//    if($partner->getShoptitle()!='')
//      $this->getLayout()->getBlock('head')->setTitle($partner->getShoptitle());
//    else
//      $this->getLayout()->getBlock('head')->setTitle($partner->getProfileurl());
//    $this->getLayout()->getBlock('head')->setKeywords($partner->getMetaKeyword());		
//    $this->getLayout()->getBlock('head')->setDescription($partner->getMetaDescription());
//        return $this;
//    }

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

  /*public function getToolbarBlock() {
     if ($blockName = $this->getToolbarBlockName()) {
          if ($block = $this->getLayout()->getBlock($blockName)) {
              return $block;
          }
      }
      $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
      return $block;
  }*/

  public function getMode() {
      return $this->getChild('toolbar')->getCurrentMode();
  }

  public function getToolbarHtml() {
      return $this->getChildHtml('toolbar');
  }

  public function getColumnCount() {
     return 4;
  }
}
?>
