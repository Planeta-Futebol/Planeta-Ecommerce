<?php

class Super_Awesome_Block_Adminhtml_Report_Simple_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridSimple');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('awesome/report_simple_collection');

    }

    protected function _prepareColumns()
    {
        $this->addColumn('description', array(
                'header'    => Mage::helper('reports')->__('Description'),
                'index'     =>'description',
                'sortable'  => false
        ));

        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn('value', array(
                'header'    =>Mage::helper('reports')->__('Value'),
                'index'     =>'value',
                'currency_code' => $currencyCode,
                'total'     =>'sum',
                'type'      =>'currency'
        ));

        $this->addExportType('*/*/exportSimpleCsv', Mage::helper('reports')->__('CSV'));

        return parent::_prepareColumns();
    }
}