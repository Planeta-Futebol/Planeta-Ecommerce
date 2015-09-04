<?php
class Franchise_Stock_Block_Saleperpartner extends Mage_Core_Block_Template
{
  public function __construct() {
    parent::__construct();

    $post = $this->getRequest()->getPost();
    $userid = Mage::getSingleton('customer/session')->getCustomer()->getId();

    $fromdate = trim($post['fromdate']);
    $todate = trim($post['todate']);
    $firsit = $fromdate." 00:00:00";
    $last = $todate." 24:00:00";
    $name = trim($post['searchfr']);
    $alias = 'name_table';

    $collection = Mage::getModel("stock/Saleperpartner")->getCollection();
    $collection->addFieldToFilter('userid',array('eq'=>$userid));



    $collection->getSelect()->joinLeft(
        array('cust' => $collection->getTable('catalog/product')),
        'cust.entity_id = main_table.stockprodid', array('*'));

    $attribute = Mage::getSingleton('eav/config')
      ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');

    if(!empty($name) && $name!="Por nome do produto") {
      $collection->getSelect()
        ->join(array($alias => $attribute->getBackendTable()),
              "main_table.stockprodid = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
              array('name' => 'value'))->where("name_table.value LIKE ?", "%$name%");
    } else {
      $collection->getSelect()
        ->join(array($alias => $attribute->getBackendTable()),
              "main_table.stockprodid = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
              array('name' => 'value'));
    }




    if(!empty($fromdate) && !empty($todate)) {
      $collection->addFieldToFilter('sale_at', array(
        'from' => $first,
        'to' => $last,
        'date' => true
      ));
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

  public function getSortBy() {
    return 'collection_id';
  }

  public function getToolbarBlock() {
    $block = $this->getLayout()->createBlock('stock/toolbar', microtime());
    return $block;
  }

  public function getMode() {
    return $this->getChild('toolbar')->getCurrentMode();
  }

  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }

  public function getToolbarHtml() {
    return $this->getChildHtml('toolbar');
  }

  public function getColumnCount() {
    return 4;
  }
}
