<?php

/**
 * Created by PhpStorm.
 * User: Ronildo
 * Date: 02/12/15
 * Time: 15:06
 */
class Reports_BillingCustomer_Block_Adminhtml_BillingCustomer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {

        echo 'block novo relatorio';
        exit;

        $this->_controller = 'adminhtml_billingcustomer';
        $this->_blockGroup = 'billingcustomer';
        $this->_headerText = Mage::helper('billingcustomer')->__('Faturamento por Cliente');
        parent::__construct();
        $this->_removeButton('add');

    }
}