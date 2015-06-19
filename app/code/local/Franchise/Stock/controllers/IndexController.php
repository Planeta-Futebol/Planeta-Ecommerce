<?php
/*
 * Controller class has to be inherited from Mage_Core_Controller_action
 */
class Franchise_Stock_IndexController extends Mage_Core_Controller_Front_Action
{
  public function indexAction() {
    /*
     * Initialization of Mage_Core_Model_Layout model
     */
    $this->loadLayout();
    $this->renderLayout();
  }
}
?>
