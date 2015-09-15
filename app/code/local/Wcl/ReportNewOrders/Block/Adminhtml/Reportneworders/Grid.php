<?php
class Wcl_ReportNewOrders_Block_Adminhtml_ReportNewOrders_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('reportnewordersGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    protected function _prepareCollection() {
        parent::_prepareCollection();
        // Get the data collection from the model

        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);

            if (!isset($data['report_from'])) {
                // getting all reports from 2001 year
                $date = new Zend_Date(mktime(0,0,0,1,1,2001));
                $data['report_from'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            if (!isset($data['report_to'])) {
                // getting all reports from 2001 year
                $date = new Zend_Date();
                $data['report_to'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            if((int) $data['report_type_products'] !== 0) {
                $arrTypeAndGroupProducts = explode('-', $data['report_type_products']);

                $data['report_group_products'] = $arrTypeAndGroupProducts[0];
                $data['report_type_products'] = $arrTypeAndGroupProducts[1];
            }

            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        /** @var Wcl_ReportNewOrders_Helper_Data $helper */
        $helper = Mage::helper('reportneworders');

        $helper->setFilters($this->_filters);

        if(!$data['report_type_salesman'] > 0){
            $this->getCollection()->initReport('reportneworders/reportneworders');
        }else{
            $this->getCollection()->initReport('reportneworders/reportfranchiseesorders');
        }


        return $this;
    }

    protected function _prepareColumns() {
        // Add columns to the grid
        $this->addColumn('sku', array(
                'header' => Mage::helper('reportneworders')->__('SKU'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'sku',
        ));

        $this->addColumn('order_items_name', array(
                'header' => Mage::helper('reportneworders')->__('Item Name'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'order_items_name'
        ));

        $this->addColumn('qty_ordered', array(
                'header'    =>Mage::helper('reports')->__('Quantity Ordered'),
                'align'     =>'right',
                'sortable' => false,
                'type'      =>'number',
                'index' => 'qty_ordered'
        ));

        $this->addColumn('unic_price', array(
                'header' => Mage::helper('reportneworders')->__('Valor Unitario'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'unic_price'
        ));

        $this->addColumn('total_sold', array(
                'header' => Mage::helper('reportneworders')->__('Valor total'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_sold'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('reportneworders')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('reportneworders')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return false;
    }

    public function getReport($from, $to) {

        if ($from == '') {
            $from = $this->getFilter('report_from');
        }
        if ($to == '') {
            $to = $this->getFilter('report_to');
        }

        $totalObj = Mage::getModel('reports/totals');
        $totals = $totalObj->countTotals($this, $from, $to);
        $this->setTotals($totals);
        $this->addGrandTotals($totals);

        return $this->getCollection()->getReport($from, $to);
    }
}