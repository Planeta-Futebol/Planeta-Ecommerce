<?php
require_once 'Mage/Customer/controllers/AccountController.php';
class Franchise_Stock_StockaccountController extends Mage_Customer_AccountController
{
  public function indexAction(){
    $this->loadLayout();
    $this->renderLayout();
  }

  public function myproductslistAction() {
    if($this->getRequest()->isPost()) {
      if (!$this->_validateFormKey()) {
        return $this->_redirect('stock/stockaccount/myproductslist/');
      }

      $collection_product = Mage::getModel('stock/product')
        ->getCollection()
        ->addFieldToFilter('stockproductid',array('eq'=>$id))
        ->addFieldToFilter('userid',array('eq'=>$customerid));
    }

    $this->loadLayout( array('default','stock_account_productlist'));
    $this->_initLayoutMessages('customer/session');
    $this->_initLayoutMessages('catalog/session');
    $this->getLayout()
      ->getBlock('head')
      ->setTitle( Mage::helper('stock')
        ->__('Meu Estoque'));

    $this->renderLayout();
  }

  public function attributeListAction() {
    $post = $this->getRequest()->getPost();

    if($post){
      $sku = $post['sku'];
      $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
      $attributeValue = $product->getResource()->getAttribute('all_attribute')->getFrontend()->getValue($product);
      $result = $attributeValue;

      echo json_encode(array("status"=>"success", "res"=>$result));
    } else {
      echo json_encode(array("status"=>"error", "res"=>"There are no attributes."));
    }
  }

  public function deleteAction(){
    $urlapp=$_SERVER['REQUEST_URI'];
    $record=Mage::getModel('stock/product')->deleteProduct($urlapp);

    Mage::getSingleton('core/session')
      ->addSuccess( Mage::helper('stock')
        ->__('Produto deletado com sucesso'));

    $this->_redirect('stock/stockaccount/myproductslist/');
  }
}
?>
