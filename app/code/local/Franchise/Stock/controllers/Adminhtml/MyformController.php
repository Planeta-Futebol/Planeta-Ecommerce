<?php
class Franchise_Stock_Adminhtml_MyformController extends Mage_Adminhtml_Controller_Action
{
  public function indexAction() {
    $this->_initAction();
    $this->_addContent($this->getLayout()->createBlock('core/template')
      ->setTemplate("stock/stock_add.phtml"));

    $this->renderLayout();
  }

  protected function _initAction() {
    $this->loadLayout()
      ->_setActiveMenu('package')
      ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'),
        Mage::helper('adminhtml')->__('Item Manager'));

    return $this;
  }

  public function getStockAction() {
    $post = $this->getRequest()->getPost();

    try {
      if (empty($post)) {
          Mage::throwException($this->__('Invalid form data.'));
      }

      $wholedata=$this->getRequest()->getParams();
      $cloneid = Mage::getModel('stock/product')->saveFranchiseProduct($wholedata);

      Mage::getSingleton('core/session')
        ->addSuccess(Mage::helper('marketplace')
          ->__('Your product has been added successfully'));

      $this->_redirect('*/*/index');
    } catch (Exception $e) {
      Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    }

    $this->_redirect('*/*');
  }
}
?>
