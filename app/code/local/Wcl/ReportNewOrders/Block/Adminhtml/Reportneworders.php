<?php
class Wcl_ReportNewOrders_Block_Adminhtml_ReportNewOrders extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_reportneworders';
        $this->_blockGroup = 'reportneworders';
        $this->_headerText = Mage::helper('reportneworders')->__('Faturamento x Periodo');
        parent::__construct();
        $this->_removeButton('add');
        
    }

}