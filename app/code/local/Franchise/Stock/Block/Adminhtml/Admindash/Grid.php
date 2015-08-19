<?php
class Franchise_Stock_Block_Adminhtml_Admindash_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('stockGrid');
    $this->setUseAjax(true);
    $this->setDefaultDir('DESC');
    $this->setSaveParametersInSession(true);
    $this->setUseAjax(true);
    $this->setFilterVisibility(false);
    $this->_emptyText = Mage::helper('stock')->__('No Record Found.');
  }

  protected function _prepareCollection()
  {
    $collection = Mage::getModel("stock/Saleperpartner")->getCollection()
      ->setOrder('sale_at');

    //$attribute = Mage::getSingleton('eav/config')
    //  ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');
    //  
    //$collection->getSelect()
    //    ->join(array($alias => $attribute->getBackendTable()),
    //          "main_table.stockprodid = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
    //          array('name' => 'value'));

    $this->setCollection($collection);
    parent::_prepareCollection();        

    foreach($this->getCollection() as $sale) {
      $stid = $sale->getStockprodid();
      $mainproduct = Mage::getModel('catalog/product')->load($stid);
      $sale->name = $mainproduct->getName();
      $frid = $sale->getUserid();
      $franchise = Mage::getModel('customer/customer')->load($frid);
      $sale->email = $franchise->getEmail();
      $productprice = $sale->getPrice_bought();
      $sale->productprice = $productprice;
      $salesprice = $sale->getPrice_sold();
      $sale->saleprice = $sale->getPrice_sold();
      $qty = $sale->getQty_sold();
      $sale->profit = ($salesprice - $productprice) * $qty;
    }
  }

  protected function _prepareColumns()
  {
    $currency = (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
    $this->addColumn('index_stock_saleid', array(
      'header'    => Mage::helper('stock')->__('ID'),
      'width'     => '20px',
      'index'     => 'index_stock_saleid',
      'type'  => 'number',
      'filter_index' => 'main_table.index_stock_saleid'
    ));

    $this->addColumn('email', array(
      'header'    => Mage::helper('stock')->__('Franqueado'),
      'width'     => '150',
      'index'     => 'email',
      'filter'    => false,
      'sortable'  => false
    ));

    $this->addColumn('name', array(
      'header'    => Mage::helper('stock')->__('Produto'),
      'index'     => 'name',
      'type'  => 'string',
      'filter'    => false,
      'sortable'  => false
    ));

    $this->addColumn('qty_sold', array(
      'header'    => Mage::helper('stock')->__('Quant.'),
      'index'     => 'qty_sold',
      'type'  => 'number',
      'filter'    => false,
      'sortable'  => false
    ));

    $this->addColumn('productprice', array(
      'header'    => Mage::helper('stock')->__('Compra'),
      'index'     => 'productprice',
      'currency_code' => $currency,
      'type'  => 'price',
      'filter'    => false,
      'sortable'  => false
        ));

    $this->addColumn('saleprice', array(
      'header'    => Mage::helper('stock')->__('Venda'),
      'index'     => 'saleprice',
      'currency_code' => $currency,
      'type'  => 'price',
      'filter'    => false,
      'sortable'  => false
    ));

    $this->addColumn('profit', array(
      'header'    => Mage::helper('stock')->__('Lucro'),
      'index'     => 'profit',
      'currency_code' => $currency,
      'type'  => 'price',
      'filter'    => false,
      'sortable'  => false
    ));

      return parent::_prepareColumns();
  }

  protected function _prepareMassaction()  {
    //$this->setMassactionIdField('main_table.index_stock_saleid');
    //$this->getMassactionBlock()->setFormFieldName('index_stock_saleid');
    //$this->getMassactionBlock()->addItem('delete', array(
    //   'label'    => Mage::helper('stock')->__('Approve'),
    //   'url'      => $this->getUrl('stock/adminhtml_admindash/massapprove')
    //));
    return $this;
  }

  public function getGridUrl(){
    return $this->getUrl("*/*/grid",array("_current"=>true));
  }
}
