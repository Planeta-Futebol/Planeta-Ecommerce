<?php
/*
 * Controller class has to be inherited from Mage_Core_Controller_action
 */
class Franchise_Stock_AjaxController extends Mage_Core_Controller_Front_Action
{
  public function indexAction() {
    try {
      $likec = $this->getRequest()->getPost('likec');
      $productid = $this->getRequest()->getPost('productid');
      $count = $likec;
      $count = $count+1;
      $product = Mage::getModel('catalog/product')->load($productid);
      $product->setp_like_count($count);
      $product->getResource()->saveAttribute($product, 'p_like_count');
      $data = array();
      $data['productid'] = $productid;
      $data['count'] = $count;
      echo json_encode(array('state'=>true,'resp'=>$data));
      die;
    } catch (Exception $e) {
      echo json_encode(array('state'=>false,'resp'=>"Error"));
    }
    die;
  }
}
?>
