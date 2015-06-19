<?php
class Franchise_Stock_SellerController extends Mage_Core_Controller_Front_Action
{
  public function indexAction() {
    $this->loadLayout();
    $this->renderLayout();
  }

  public function collectionAction() {
    $this->loadLayout();
    $this->renderLayout();
  }
}
