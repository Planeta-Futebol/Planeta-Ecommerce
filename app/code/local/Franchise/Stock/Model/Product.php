<?php

class Franchise_Stock_Model_Product extends Mage_Core_Model_Abstract
{
  public function _construct() {
    parent::_construct();
    $this->_init('stock/product');
  }

  public function getFrCollection($userid) {
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

    return $collection;
  }

  protected function addProductFr($itemsku, $fruserid, $frorderid, $purchased_price, $qty, $attribute_info) {
    $product = Mage::getModel('catalog/product');
    $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $itemsku);

    //duplicate sku will be combination of franchise user id, his order id, item sku.
    $duplicate_sku = "fr_".$fruserid."_".$itemsku;

    $clone = $_product->duplicate();
    $clone->setSku($duplicate_sku);
    $clone->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
    $clone->setPrice($_product->getPrice());
    $clone->setSpecialPrice($purchased_price);
    $clone->setMrsp($_product->getMrsp());
    $allattributes = "";

    if(!empty($attribute_info)) {
      foreach($attribute_info as $attribute_inf) {
        $attrilabel = $attribute_inf['label'];
        $attrival = $attribute_inf['value'];
        $clone->set.$attrilabel.'('.$attrival.')';
        $allattributes .= $attrilabel.",";
      }
    }

    $clone->setAll_attribute($allattributes);
    $clone->setStatus(1);
    $clone->setTaxClassId(4);

    $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
    $clone->setMediaGallery (array('images'=>array (), 'values'=>array ()));

    try{
      $clone->getResource()->save($clone);
      $cloneid = $clone->getId();
      $stproduct = $product->load($cloneid);
      $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($stproduct);
      $stockItem->setData('manage_stock', 1);
      $stockItem->setData('is_in_stock', 1);
      $stockItem->setData('use_config_notify_stock_qty', 0);
      $stockItem->setData('qty', $qty);

      $stockItem->save();
      $stproduct->save();
    } catch(Exception $e){
      Mage::log($e->getMessage());
    }

    $collection = Mage::getModel('stock/product');
    $collection->setstockproductid($cloneid); // magento product id
    $collection->setuserid($fruserid);
    $collection->setstatus(1);
    $collection->setstockprodsku($itemsku);
    $collection->save();

    return $cloneid;
  }

  /* save products */
  public function saveFranchiseProduct($wholedata) {
    $frobject = new Franchise_Stock_Model_Product();
    $frorderid = $wholedata['franchise_order'];
    $fruserid = $wholedata['franchise_id'];
    $singleorder = Mage::getModel("sales/order")->loadByIncrementId($frorderid);

    if($singleorder) {
      $orderItems = $singleorder->getItemsCollection()
        ->addAttributeToSelect('*')
        ->load();

      if($orderItems) {
        foreach($orderItems as $orderItem) {
          $parentitemid = $orderItem->getParentItemId();
          $cloneid = 1;

          if(empty($parentitemid)) {
            $productoptions = $orderItem->getProductOptions();

            if(!empty($productoptions)) {
              $attribute_info = $productoptions['attributes_info'];
            } else {
              $attribute_info = array();
            }

            $allattributes = "";
            $itemsku = $orderItem->getSku();
            $qty =  (int) $orderItem->getData('qty_ordered');
            $purchased_price = $orderItem->getData('price');

            $stockskus = Mage::getModel('stock/product')->getCollection()
              ->addFieldToFilter('userid', array('eq' => $fruserid));
            $allskus = $stockskus->getColumnValues('stockprodsku');

            $dup_sku = "fr_".$fruserid."_".$itemsku;
            $repli_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$dup_sku);

            //if product exists in franchise customer's store
            if(!empty($allskus) && (in_array($itemsku, $allskus)) && !empty($repli_product)) {
              $frproductid = $repli_product->getId();
              $frprodload = Mage::getModel('catalog/product')->load($frproductid);
              $prodsku = $frprodload->getSku();
              $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($frprodload);
              $oldqty = $stockItem->getData('qty');
              $totalqty = $oldqty + $qty;
              $stockItem->setData('qty', $totalqty);
              $stockItem->setData('is_in_stock', 1);
              $stockItem->save();

              //configurable product
              if(!empty($attribute_info)) {
                foreach($attribute_info as $attribute_inf) {
                  $attrilabel = $attribute_inf['label'];
                  $attrival = $attribute_inf['value'];
                  $frprodload->set.$attrilabel.'('.$attrival.')';
                  $allattributes .= $attrilabel.",";
                }

                $frprodload->setAll_attribute($allattributes);
                $frprodload->save();
              }
            } else if(empty($repli_product)) {
              $cloneid = $frobject->addProductFr($itemsku, $fruserid, $frorderid, $purchased_price, $qty, $attribute_info);
            }
          }
        }
      }
    }

    return $cloneid;
  }
}
?>
