<?php
class Wcl_ReportNewOrders_Block_Adminhtml_ReportNewOrders extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {

        $this->_controller = 'adminhtml_reportneworders';
        $this->_blockGroup = 'reportneworders';
        $this->_headerText = Mage::helper('reportneworders')->__('Faturamento x Periodo');
        parent::__construct();
        $this->_removeButton('add');
        
    }

    protected function _prepareLayout()
    {
        $this->setChild( 'grid',
            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
                $this->_controller . '.grid')->setSaveParametersInSession(true)->setTemplate('tagon/report/grid.phtml')
        );

    }

}