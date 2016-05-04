<?php

class Reports_Inventory_Block_Adminhtml_Inventory extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct() {

        $this->_controller = 'adminhtml_inventory';
        $this->_blockGroup = 'inventory';
        $this->_headerText = Mage::helper('inventory')->__('Estoque');
        parent::__construct();
        $this->_removeButton('add');

    }

    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
                $this->_controller . '.grid')->setSaveParametersInSession(true)//->setTemplate('reports/billing/customer/grid.phtml')
        );

    }
}