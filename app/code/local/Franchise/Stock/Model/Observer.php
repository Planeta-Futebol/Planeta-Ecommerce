<?php
class Cis_Customtheme_Model_Observer
{
  //public function logCartAdd() {
  //    $product = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('product', 0));
  //    if (!$product->getId())
  //    {
  //        return;
  //    }
  //    $categories = $product->getCategoryIds();
  //    //'price' => $product->getPrice(),
  //    $data = Mage::getModel('core/session')->setProductToShoppingCart(
  //        new Varien_Object(array(
  //            'id' => $product->getId(),
  //            'qty' => Mage::app()->getRequest()->getParam('qty', 1),
  //            'name' => $product->getName(),
  //            'price' => $product->getPrice(),
  //            'category_name' => Mage::getModel('catalog/category')->load($categories[0])->getName(),
  //        ))
  //    );
  //    echo '<pre>';
  //    print_r($data);
  //    die;
  //}

  public function modifyPrice(Varien_Event_Observer $obs) {
    // Get the quote item
    $item = $obs->getQuoteItem();

    // Ensure we have the parent item, if it has one
    $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
    $product = Mage::getModel('catalog/product')
      ->load(Mage::app()
        ->getRequest()
        ->getParam('product', 0));

    $price = Mage::app()->getRequest()->getParam('price');
    //print_r($price); die;
    // Discounted 50% off
    //$percentDiscount = 0.50;
    //$percentDiscount = $product->getdiscount();
    $percentDiscount = '';
    // This makes sure the discount isn't applied over and over when refreshing
    if($percentDiscount!='') {
      $specialPrice = $product->getPrice() - ($product->getPrice() * $percentDiscount);
      // Make sure we don't have a negative
      if ($specialPrice > 0) {
          $item->setCustomPrice($specialPrice);
          $item->setOriginalCustomPrice($specialPrice);
          $item->getProduct()->setIsSuperMode(true);
      }
    }
  }

  protected function _getPriceByItem(Mage_Sales_Model_Quote_Item $item) {
    $price;
    //use $item to determine your custom price.
    return $price;
  }

  //public function afterSave($object)
  //{
  //    $value = $object->getData($this->getAttribute()->getName());
  //
  //    if (is_array($value) && !empty($value['delete'])) {
  //        $object->setData($this->getAttribute()->getName(), '');
  //        $this->getAttribute()->getEntity()
  //            ->saveAttribute($object, $this->getAttribute()->getName());
  //        return;
  //    }
  //
  //    $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS;
  //    try {
  //        $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
  //
  //        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','pdf','doc','txt','sql'));
  //        $uploader->setAllowRenameFiles(true);
  //        $result = $uploader->save($path);
  //        //allowed extensions here
  //        $object->setData($this->getAttribute()->getName(), $result['file']);
  //        $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
  //    } catch (Exception $e) {
  //        if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
  //            Mage::logException($e);
  //        }
  //        return;
  //    }
  //}
}
?>
