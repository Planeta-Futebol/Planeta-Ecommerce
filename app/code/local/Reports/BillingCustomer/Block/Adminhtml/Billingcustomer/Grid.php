<?php

/**
 * Config the column grid system and sets the
 * collection that will be used to recover data.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_BillingCustomer_Block_Adminhtml_BillingCustomer_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Values for filters submitted by the form.
     *
     * @var array
     */
    private $filterData = array();

    /**
     * Starts standards values and calling default constructor parent class.
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setId('billingcustomerGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    public function getTotals()
    {
        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('billingcustomer');

        return $helper->getTotals();
    }

    public function getGrandTotals()
    {


        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('billingcustomer');

        $subTotals = $helper->getSubTotals();



        $totals = new Varien_Object();
        $fields = array(
            'qty_order' => 0, //actual column index, see _prepareColumns()
            'total_amount_refunded' => 0,
            'total_sold' => 0,
        );

        foreach ($subTotals as $item) {
            foreach($fields as $field=>$value){
                $fields[$field]+=$item[$field];
            }
        }

        $totals->setData($fields);

        return $totals;
    }

    /**
     * Sets the collection of data to be used in the report.
     *
     * @return $this
     */
    protected function _prepareCollection() {
        parent::_prepareCollection();
        // Get the data collection from the model

        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('billingcustomer');

        $helper->setFilters($this->_filters);

        $this->getCollection()->initReport('billingcustomer/billingcustomer');

        return $this;
    }

    /**
     * Prepare the columns system to display the data.
     *
     * @return $this|void
     * @throws Exception
     */
    protected function _prepareColumns() {

        $this->addColumn('full_name_cutomer', array(
            'header'   => Mage::helper('billingcustomer')->__('Nome do Cliente'),
            'align'    => 'left',
            'sortable' => false,
            'index'    => 'full_name_cutomer'
        ));

        $this->addColumn('state', array(
            'header'   => Mage::helper('billingcustomer')->__('Estado'),
            'align'    => 'left',
            'sortable' => false,
            'index'    => 'state'
        ));

        $this->addColumn('representative_name', array(
            'header'   => Mage::helper('billingcustomer')->__('Nome do Representante'),
            'align'    => 'left',
            'sortable' => false,
            'index'    => 'representative_name'
        ));

        $this->addColumn('qty_order', array(
            'header'    => Mage::helper('billingcustomer')->__('Quantidade de Pedidos'),
            'align'     => 'right',
            'sortable'  => false,
            'type'      => 'number',
            'index'     => 'qty_order',
        ));

        $this->addColumn('total_amount_refunded', array(
            'header'    => Mage::helper('billingcustomer')->__('Valor devolvido'),
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'total_amount_refunded'
        ));

        $this->addColumn('total_sold', array(
            'header'    => Mage::helper('billingcustomer')->__('Valor de Compras'),
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'total_sold'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('billingcustomer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('billingcustomer')->__('XML'));

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

    /**
     * Retrieve the values informed the form, if a key is informed
     * retrieves the key value, if not, retrieves all valore as an array.
     *
     * @param null $key
     * @return array
     */
    private function getFilterData( $key = null )
    {
        $data = array();

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

            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        return (is_null($key)) ? $data : $data[$key];
    }


}