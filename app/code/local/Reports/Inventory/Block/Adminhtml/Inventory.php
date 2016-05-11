<?php

/**
 * Set default configuratinos of grid layout container.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_Inventory_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Define controller, group blocks and set title report.
     *
     *
     */
    public function __construct() {

        $this->_controller = 'adminhtml_inventory';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Estoque');
        parent::__construct();
        $this->_removeButton('add');

    }
}