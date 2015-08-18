<?php
require_once("Mage/Customer/controllers/AccountController.php");

class Help_Customer_AccountController extends Mage_Customer_AccountController
{

    public function faqAction()
    {

        $this->loadLayout();

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('FAQ'));
        $this->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        $this->renderLayout();
    }
}