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
      if($qty){
        $rowdata[] = $value->getStockproductid();
      }
    }
    $collection = Mage::getModel('catalog/product')->getCollection();
    $collection->addAttributeToSelect('*');
    $collection->addAttributeToFilter('entity_id', array('in' => $rowdata));
    return $collection;
  }
  
  public function addProductFr($itemsku, $fruserid, $frorderid, $purchased_price, $qty, $attribute_info) {
    //if products does not exists in franchise customer's store
    $product = Mage::getModel('catalog/product');
    $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $itemsku);
    //duplicate sku will be combination of franchise user id, his order id, item sku.
    $duplicate_sku = "fr_".$fruserid."_".$itemsku;
    $saleprice = $_product->getPrice();
    $clone = $_product->duplicate();
    $clone->setSku($duplicate_sku);
    $clone->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
    $clone->setPrice($saleprice);
    $clone->setSpecialPrice($purchased_price); // purchased_price at which franchise has ordered which is at discount.
    $allattributes = "";

    if(!empty($attribute_info)) {
      foreach($attribute_info as $attribute_inf) {
        $attrilabel = $attribute_inf['label'];
        $attrival = $attribute_inf['value'];
        $clone->set.$attrilabel.'('.$attrival.')';
        $allattributes .= $attrilabel.",";
      }
      $clone->setAll_attribute($allattributes);
    }
    
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
        $stockItem1->setData('is_in_stock', 1);
        $stockItem1->setData('use_config_notify_stock_qty', 0);
        $stockItem1->setData('qty', $qty);
        $stockItem1->save();
        $stproduct->save();
    } catch(Exception $e){
        Mage::log($e->getMessage());
    }

    $vendorId = Mage::getSingleton('customer/session')->getCustomer()->getId();
    $collection1=Mage::getModel('stock/product');
    $collection1->setstockproductid($cloneid); // magento product id
    $collection1->setuserid($fruserid);
    $collection1->setstatus(1);
    $collection1->setstockprodsku($itemsku);
    $collection1->save();
    
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
      $stockskus = Mage::getModel('stock/product')->getCollection()->addFieldToFilter('userid', array('eq' => $fruserid));
      $allskus = $stockskus->getColumnValues('stockprodsku');
      
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
            $items = array();
            $itemsku = $orderItem->getSku();
            $items['itemname'] = $orderItem->getName();
            $items['productid'] = $orderItem->getProductId();
            $qty =  (int) $orderItem->getData('qty_ordered');
            $items['itemprice'] =  $orderItem->getData('price');
            $purchased_price = $items['itemprice'];
            
            $dup_sku = "fr_".$fruserid."_".$itemsku;
            $repli_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$dup_sku);

            if(!empty($allskus) && (in_array($itemsku, $allskus)) && !empty($repli_product)) {
              $frproductid = $repli_product->getId();
              //if product exists in franchise customer's store
              $frprodload = Mage::getModel('catalog/product')->load($frproductid);
              //for increasing the stock of already existing product
              $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($frprodload);
                //associated product
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
              //stock is empty
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
