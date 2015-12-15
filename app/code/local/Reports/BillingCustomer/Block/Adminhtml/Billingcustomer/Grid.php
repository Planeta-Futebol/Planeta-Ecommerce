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
     * @var Varien_Object
     */
    private $filtersData = null;

    /**
     * Array subtotal values
     *
     * @var array
     */
    private $subTotals = array();

    /**
     * Key to use the special check made in
     *
     * @var int
     */
    private $key = 0;

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

        $this->_exportVisibility = false;

        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('billingcustomer');

        $paramData = $this->getParamData();

        $this->filtersData = $paramData;

        $helper->setFilters($paramData);
    }

    /**
     * @return Varien_Object
     */
    public function getGrandTotals()
    {
        $subTotals = $this->subTotals;

        $totals = new Varien_Object();
        $fields = array(
            'qty_products_sold'     => 0, //actual column index, see _prepareColumns()
            'qty_order'             => 0,
            'qty_order_canceled'    => 0,
            'qty_order_closed'      => 0,
            'total_sold'            => 0,
            'total_order_canceled'  => 0,
            'total_amount_refunded' => 0,
            'dicount_amount'        => 0,
            'shipping_amount'       => 0
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
     * Returns the subtotal for each key informed that exists in the collection.
     *
     * @return Varien_Object
     */
    public function getTotals()
    {
        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('billingcustomer');

        $totals = new Varien_Object();

        $fields = array(
            'qty_products_sold'     => 0, //actual column index, see _prepareColumns()
            'qty_order'             => 0,
            'qty_order_canceled'    => 0,
            'qty_order_closed'      => 0,
            'total_sold'            => 0,
            'total_order_canceled'  => 0,
            'total_amount_refunded' => 0,
            'dicount_amount'        => 0,
            'shipping_amount'       => 0
        );

        foreach ($helper->getCollection() as $item) {
            foreach($fields as $field=>$value){
                $fields[$field]+=$item->getData($field);
            }
        }

        $totals->setData($fields);

        /*
         * This validation is because the method is being called once every column gride,
         * thus accumulating wrong values for the grand total of calculation.
         */
        if($this->key++ % sizeof($this->getColumns()) == 0) {
            $this->subTotals[] = $fields;
        }

        return $totals;
    }

    /**
     * Get a filter value of the parameters passed by the form.
     *
     * @param  $key string
     *
     * @return Varien_Object
     */
    public function getFiltersData( $key = null )
    {
        return (!is_null($this->filtersData)) ? $this->filtersData->getData($key) : null;
    }

    /**
     * Sets the collection of data to be used in the report and get param data
     * to use in filter collection.
     *
     * @return $this
     */
    protected function _prepareCollection() {
        parent::_prepareCollection();

        if($this->getFiltersData("export_csv")){
            $this->_exportVisibility = true;
        }

        // Get the data collection from the model
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
        if($this->getFiltersData("export_csv")) {
            $this->addColumn('date_customer_register', array(
                'header' => Mage::helper('billingcustomer')->__('Data do Cadastro'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'date_customer_register'
            ));
            $this->addColumn('full_name_cutomer', array(
                'header' => Mage::helper('billingcustomer')->__('Nome do Cliente'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'full_name_cutomer'
            ));

            $this->addColumn('customer_email', array(
                'header' => Mage::helper('billingcustomer')->__('E-mail do Cliente'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'customer_email'
            ));

            $this->addColumn('group_cutomer', array(
                'header' => Mage::helper('billingcustomer')->__('Grupo do Cliente'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'group_cutomer'
            ));

            $this->addColumn('affiliateplus_coupon', array(
                'header' => Mage::helper('billingcustomer')->__('Código de Afiliado'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'affiliateplus_coupon'
            ));

            $this->addColumn('state', array(
                'header' => Mage::helper('billingcustomer')->__('Estado'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'state'
            ));

            $this->addColumn('representative_name', array(
                'header' => Mage::helper('billingcustomer')->__('Nome do Representante'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'representative_name'
            ));

            $this->addColumn('qty_products_sold', array(
                'header' => Mage::helper('billingcustomer')->__('Produtos Vendidos'),
                'align' => 'right',
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_products_sold',
            ));

            $this->addColumn('qty_order', array(
                'header' => Mage::helper('billingcustomer')->__('Quantidade de Pedidos'),
                'align' => 'right',
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_order',
            ));

            $this->addColumn('qty_order_canceled', array(
                'header' => Mage::helper('billingcustomer')->__('Pedidos Cancelados'),
                'align' => 'right',
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_order_canceled',
            ));

            $this->addColumn('qty_order_closed', array(
                'header' => Mage::helper('billingcustomer')->__('Pedidos Devolvidos/Fechados'),
                'align' => 'right',
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_order_closed',
            ));

            $this->addColumn('total_sold', array(
                'header' => Mage::helper('billingcustomer')->__('Total dos Pedidos'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_sold'
            ));

            $this->addColumn('total_order_canceled', array(
                'header' => Mage::helper('billingcustomer')->__('Total dos Pedidos Cancelados'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_order_canceled'
            ));

            $this->addColumn('total_amount_refunded', array(
                'header' => Mage::helper('billingcustomer')->__('Total Devoluções'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_amount_refunded'
            ));

            $this->addColumn('dicount_amount', array(
                'header' => Mage::helper('billingcustomer')->__('Total Desconto'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'dicount_amount'
            ));

            $this->addColumn('shipping_amount', array(
                'header' => Mage::helper('billingcustomer')->__('Total Frete'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'shipping_amount'
            ));
        }else{

            $this->addColumn('qty_order', array(
                'header' => Mage::helper('billingcustomer')->__('Pedidos'),
                'align' => 'right',
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_order',
            ));

            $this->addColumn('qty_products_sold', array(
                'header' => Mage::helper('billingcustomer')->__('Produtos Vendidos'),
                'align' => 'right',
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_products_sold',
            ));

            $this->addColumn('total_sold', array(
                'header' => Mage::helper('billingcustomer')->__('Total de Vendas'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_sold'
            ));

            $this->addColumn('total_invoiced', array(
                'header' => Mage::helper('billingcustomer')->__('Total Faturado'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_invoiced'
            ));

            $this->addColumn('total_amount_refunded', array(
                'header' => Mage::helper('billingcustomer')->__('Devolvido'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_amount_refunded'
            ));

            $this->addColumn('sales_tax', array(
                'header' => Mage::helper('billingcustomer')->__('Imposto de Vendas'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'sales_tax'
            ));


            $this->addColumn('shipping_amount', array(
                'header' => Mage::helper('billingcustomer')->__('Frete de Vendas'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'shipping_amount'
            ));


            $this->addColumn('dicount_amount', array(
                'header' => Mage::helper('billingcustomer')->__('Disconto de Vendas'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'dicount_amount'
            ));

            $this->addColumn('total_order_canceled', array(
                'header' => Mage::helper('billingcustomer')->__('Cancelados'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_order_canceled'
            ));
        }

        $this->addExportType('*/*/exportCsv', Mage::helper('billingcustomer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('billingcustomer')->__('XML'));

        return parent::_prepareColumns();
    }


    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row) {
        return false;
    }

    /**
     * @param $from
     * @param $to
     * @return mixed
     */
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
     * Retrieve the values informed the form.
     *
     * @return Varien_Object
     */
    private function getParamData()
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

        $filters = new Varien_Object();
        return $filters->setData($data);
    }
}