<?php
class Franchise_Stock_Model_Saleperpartner extends Mage_Core_Model_Abstract
{
  public function _construct() {
    parent::_construct();
    $this->_init('stock/Saleperpartner');
  }

  public function getSaleperpartner() {
    //$collection = Mage::getResourceModel('catalog/product_collection')
    //            ->addAttributeToSelect('*');
    //$collection->getSelect()->join(array('mep' => "stock_Saleperpartner"), "e.entity_id = mep.stockprodid", array('mep.*'));
    $collection->getSelect()->joinRight(
        array('cust' => $collection->getTable('catalog/product')),
        'cust.entity_id = main_table.stockprodid');
    return $collection;
  }

  public function saveSaleReport($wholedata) {
    //only one time saved. need to enter new record each time.
    $sku = $wholedata['sku'];
    $saledata['stockprodid'] = $wholedata['frprodid'];

    if(Mage::getSingleton('customer/session')->isLoggedIn()) {
      $franchisedata = Mage::getSingleton('customer/session')->getCustomer();
      $saledata['userid'] = $franchisedata->getId();
    }

    $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
    $saledata['qty_sold'] = $wholedata['qty_sold'];
    $saledata['price_sold'] = $wholedata['price_sold'];
    $saledata['price_bought'] = $_product->getSpecialPrice();
    $saledata['sale_at']=date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));

    $collection=Mage::getModel('stock/Saleperpartner');
    $collection->setData($saledata);
    $collection->save();

    $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product);
    $oldqty = (int) $stockItem->getData('qty');
    $newqty = $oldqty - $wholedata['qty_sold'];
    $stockItem->setData('qty', $newqty);
    $stockItem->setData('is_in_stock', 1);
    $stockItem->save();
    return 1;
  }
}
?>
