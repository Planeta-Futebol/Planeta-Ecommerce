<?php
class Franchise_Stock_Block_Collection  extends Mage_Catalog_Block_Product_Abstract
{
  //protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
  public function __construct() {
    parent::__construct();

    $userid = Mage::getSingleton('customer/session')->getCustomer()->getId();
    $post = $this->getRequest()->getPost();
    $rowdata=array();

    $querydata = Mage::getModel('stock/product')->getCollection()
        ->addFieldToFilter('userid', array('eq' => $userid))
        ->addFieldToFilter('status', array('neq' => 2))
        ->setOrder('stockproductid');

    foreach ($querydata as  $value) {
      $qty = (int)Mage::getModel('cataloginventory/stock_item')
                ->loadByProduct($value->getStockproductid())->getQty();
      if($qty) {
        $rowdata[] = $value->getStockproductid();
      }
    }

    $collection = Mage::getModel('catalog/product')->getCollection();
    $collection->addAttributeToSelect('*');
    $collection->addAttributeToFilter('entity_id', array('in' => $rowdata));
    $collection->addAttributeToSort('name', 'ASC');

    //filter with search content
    if(!empty($post)) {
      $collection->addAttributeToFilter(array(
                  array('attribute'=>'name', 'like'=>'%'.$post['searchfr'].'%')));
    }

    $this->setCollection($collection);
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
?>
