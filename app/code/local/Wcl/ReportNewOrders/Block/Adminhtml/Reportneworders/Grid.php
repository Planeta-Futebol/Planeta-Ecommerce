<?php

/**
 * Class Wcl_ReportNewOrders_Block_Adminhtml_ReportNewOrders_Grid
 */
class Wcl_ReportNewOrders_Block_Adminhtml_ReportNewOrders_Grid extends Mage_Adminhtml_Block_Report_Grid
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
        $this->setId('reportnewordersGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    /**
     * Sets the collection of data to be used in the report.
     *
     * @return $this
     */
    protected function _prepareCollection() {
        parent::_prepareCollection();
        // Get the data collection from the model

        /** @var Wcl_ReportNewOrders_Helper_Data $helper */
        $helper = Mage::helper('reportneworders');

        $helper->setFilters($this->getFilterData());

        if(!$this->getFilterData('report_type_salesman') > 0){
            $this->getCollection()->initReport('reportneworders/reportneworders');
        }else{
            $this->getCollection()->initReport('reportneworders/reportfranchiseesorders');
        }

        return $this;
    }

    /**
     * Prepare the columns system to display the data.
     *
     *
     * @return $this|void
     * @throws Exception
     */
    protected function _prepareColumns() {
        // Add columns to the grid
        $this->addColumn('customer_type', array(
            'header' => Mage::helper('reportneworders')->__('Tipo Cliente'),
            'align' => 'left',
            'sortable' => true,
            'index' => 'customer_type',
        ));

        $this->addColumn('customer_state', array(
            'header' => Mage::helper('reportneworders')->__('Estado'),
            'align' => 'left',
            'sortable' => true,
            'index' => 'customer_state',
        ));

        $this->addColumn('order_increment_id', array(
                'header' => Mage::helper('reportneworders')->__('Número do Pedido'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'order_increment_id',
        ));

        $this->addColumn('sku', array(
                'header' => Mage::helper('reportneworders')->__('SKU'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'sku',
        ));

        $this->addColumn('product_size', array(
            'header' => Mage::helper('reportneworders')->__('Tamanho'),
            'align' => 'left',
            'sortable' => true,
            'index' => 'product_size',
        ));

        $this->addColumn('product_gender', array(
            'header' => Mage::helper('reportneworders')->__('Gênero'),
            'align' => 'left',
            'sortable' => true,
            'index' => 'product_gender',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'
        ));

        $this->addColumn('product_category', array(
            'header' => Mage::helper('reportneworders')->__('Categoria'),
            'align' => 'left',
            'sortable' => true,
            'index' => 'product_category',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'
        ));

        $this->addColumn('product_clothing', array(
            'header' => Mage::helper('reportneworders')->__('Vestuário'),
            'align' => 'left',
            'sortable' => true,
            'index' => 'product_clothing',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'
        ));

        $this->addColumn('product_style', array(
            'header' => Mage::helper('reportneworders')->__('Estilo'),
            'align' => 'left',
            'sortable' => true,
            'index' => 'product_style',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'
        ));


        $this->addColumn('order_items_name', array(
                'header' => Mage::helper('reportneworders')->__('Nome do Produto'),
                'align' => 'left',
                'sortable' => false,
                'index' => 'order_items_name'
        ));

        $this->addColumn('product_cost', array(
                'header'    =>Mage::helper('reports')->__('Custo'),
                'align'     =>'right',
                'sortable' => false,
                'type'      =>'number',
                'index' => 'product_cost',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'

        ));

        $this->addColumn('qty_ordered', array(
                'header'    =>Mage::helper('reports')->__('Quantity Ordered'),
                'align'     =>'right',
                'sortable' => false,
                'type'      =>'number',
                'index' => 'qty_ordered'
        ));

        if(!$this->getFilterData('report_type_salesman') > 0) {
            $this->addColumn('discount_amount', array(
                'header' => Mage::helper('reportneworders')->__('Desconto'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'discount_amount'
            ));

            $this->addColumn('qty_refunded', array(
                'header' => Mage::helper('reportneworders')->__('Quantidade devolvida'),
                'align' => 'right',
                'sortable' => false,
                'type' => 'number',
                'index' => 'qty_refunded'
            ));
            $this->addColumn('amount_refunded', array(
                'header' => Mage::helper('reportneworders')->__('Valor devolvido'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'amount_refunded'
            ));
        }else{
            $this->addColumn('unic_price', array(
                'header' => Mage::helper('reportneworders')->__('Valor unitario'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'unic_price'
            ));
        }

        $this->addColumn('total_sold', array(
                'header' => Mage::helper('reportneworders')->__('Valor Bruto de Venda'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_sold'
        ));

        $this->addColumn('total_liquid', array(
                'header' => Mage::helper('reportneworders')->__('Valor Recebido (- Frete)'),
                'align' => 'left',
                'sortable' => true,
                'index' => 'total_liquid'
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

    /**
     * Retrieve the values informed the form, if a key is informed
     * retrieves the key value, if not, retrieves all valore as an array.
     *
     * @param null $key
     * @return array
     */
    private function getFilterData( $key = null )
    {
        $filter = $this->getParam($this->getVarNameFilter(), null);
        $data = array();
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

            if($data['report_type_products'] != '0') {
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

        return (is_null($key)) ? $data : $data[$key];
    }
}