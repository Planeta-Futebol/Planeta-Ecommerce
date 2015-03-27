<?php

class Franchise_Stock_Model_Product extends Mage_Core_Model_Abstract
{
  public function _construct() {
    parent::_construct();
    $this->_init('stock/product');
  }

  /* save products */
  public function saveFranchiseProduct($wholedata){
    //need to apply check for the same sku.
    $frorderid = $wholedata['franchise_order'];
    $fruserid = $wholedata['franchise_id'];
    $singleorder = Mage::getModel("sales/order")->loadByIncrementId($frorderid);

    if($singleorder) {
      $orderItems = $singleorder->getItemsCollection()
        ->addAttributeToSelect('*')
        ->load();

      $items = array();
      $totalorder = $orderItems.count();

      if($orderItems) {
        $i=1;

        foreach($orderItems as $orderItem) {
          $item = array();
          $item['itemsku'] = $orderItem->getSku();
          $item['itemqty'] =  (int) $orderItem->getData('qty_ordered');
          $item['itemprice'] =  $orderItem->getData('price');
          $items[$i] = $item;
          $i++;
        }
      }
      $arrofexistssku = array();

      foreach($items as $temp_item) {
        $itemsku = $temp_item['itemsku'];
        $qty = $temp_item['itemqty'];
        $collects = Mage::getModel('stock/product')->getCollection();

        foreach($collects as $collect) {
          $frproductid = $collect->getData('stockproductid');
          $frprodload = Mage::getModel('catalog/product')->load($frproductid);
          $prodsku = $frprodload->getSku();
          $exists = explode("_",$prodsku);
          $existssku = trim($exists[3],"");

          if($existssku == trim($itemsku,"")) {
            $arrofexistssku[] = $itemsku;
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($frprodload);
            $oldqty = $stockItem->getData('qty');
            $totalqty = $oldqty + $qty;
            $stockItem->setData('qty', $totalqty);
            $stockItem->save();
            $frprodload->save();
          }
        }

        if(!in_array($itemsku, $arrofexistssku))
        {
          $product = Mage::getModel('catalog/product');
          $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $itemsku);

          //duplicate sku will be combination of franchise user id, his order id, item sku.
          $duplicate_sku = "fr_".$fruserid."_".$frorderid."_".$itemsku;
          $purchased_price = $temp_item['itemprice'];
          $saleprice = $_product->getPrice();

          $clone = $_product->duplicate();
          $clone->setSku($duplicate_sku);
          $clone->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
          $clone->setPrice($saleprice);
          $clone->setSpecialPrice($purchased_price); // purchased_price at which franchise has ordered which is at discount.
          $qty = $temp_item['itemqty'];

          $clone->setStatus(1);
          $clone->setTaxClassId(4);

          $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
          $clone->setMediaGallery (array('images'=>array (), 'values'=>array ()));

          try{
            $clone->getResource()->save($clone);
            $cloneid = $clone->getId();
            $stproduct = $product->load($cloneid);
            $stockItem1 = Mage::getModel('cataloginventory/stock_item')->loadByProduct($stproduct);
            $stockItem1->setData('manage_stock', 1);
            $stockItem1->setData('is_in_stock', 0);
            $stockItem1->setData('use_config_notify_stock_qty', 0);
            $stockItem1->setData('qty', $qty);
            $stockItem1->save();
            $stproduct->save();
          } catch(Exception $e){
            Mage::log($e->getMessage());
          }

          $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
          $collection = Mage::getModel('stock/product');
          $collection->setstockproductid($cloneid); // magento product id
          $collection->setuserid($fruserid);
          $collection->setstatus(1);
          $collection->save();
        }
      }
    }

    return $cloneid;
  }
}
?>
