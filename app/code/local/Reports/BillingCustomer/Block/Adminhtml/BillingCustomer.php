<?php

/**
 * Set default configuratinos of grid layout container.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer_Block_Adminhtml_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_BillingCustomer_Block_Adminhtml_BillingCustomer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Define controller, group blocks and set title report.
     *
     */
    public function __construct() {

        $this->_controller = 'adminhtml_billingcustomer';
        $this->_blockGroup = 'billingcustomer';
        $this->_headerText = Mage::helper('billingcustomer')->__('Faturamento por Cliente');
        parent::__construct();
        $this->_removeButton('add');

    }

    /**
     * Define a new file .phtml layout for grid system of the report.
     *
     */
    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
                $this->_controller . '.grid')->setSaveParametersInSession(true)->setTemplate('reports/billing/customer/grid.phtml')
        );

    }

}