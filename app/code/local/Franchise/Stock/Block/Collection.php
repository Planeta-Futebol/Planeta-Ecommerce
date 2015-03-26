<?php
class Franchise_Stock_Block_Collection  extends Mage_Catalog_Block_Product_Abstract
{
  //protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';
  public function __construct() {
    parent::__construct();

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

    $collection->addAttributeToFilter('entity_id', array('in' => $rowdata));
    $this->setCollection($collection);
  }

  protected function _prepareLayout() {
    parent::_prepareLayout();
    $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');

    //$pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
    $pager->setAvailableLimit(array(1=>1,2=>2,3=>3,'all'=>'all'));
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

  public function getColumnCount() {
     return 4;
  }
}
?>
