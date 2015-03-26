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

      foreach($items as $temp_item) {
        $itemsku = $temp_item['itemsku'];
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

        //$stockArray = array(
        //  'use_config_manage_stock' => 0,
        //  'manage_stock' => 1,
        //  'qty' => $qty,
        //  'is_in_stock' => 1,
        //);
        //$clone->setStockData($stockArray);

        $clone->setStatus(1);
        $clone->setTaxClassId(4);

        $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
        $clone->setMediaGallery (array('images'=>array (), 'values'=>array ()));
        try{
          $clone->getResource()->save($clone);
          $cloneid = $clone->getId();
        //  $new_product = $product->load($cloneid);
        //  $new_product->addImageToMediaGallery($image_url, array ('image', 'small_image', 'thumbnail'), true, false);
        //  $attributes = $new_product->getTypeInstance(true)->getSetAttributes($new_product);
        //  $gallery = $attributes['media_gallery'];
        //  $images = $new_product->getMediaGalleryImages();
        //  foreach ($images as $image) {
        //    $backend = $gallery->getBackend();
        //    $backend->updateImage(
        //      $new_product,
        //      $image->getFile(),
        //      array('position' => $position)
        //    );
        //  }
        //  $new_product->getResource()->saveAttribute($new_product, 'media_gallery');
        //  $new_product->save();

          $stproduct = $product->load($cloneid);
          $stproduct->setStockData(array(
            'manage_stock' => 1,
            'use_config_manage_stock' => 0,
            'is_in_stock' => 1,
            'qty' => 2
           ));
          $stproduct->save();

        //$stproduct->getResource()->save($stproduct);
        //  $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($clone->getId());
        //  foreach($stockArray as $key => $val){
        //    $stockItem->setData($key, $val);
        //  }
        //  $stockItem->save();
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

    return $cloneid;
  }
}
?>
