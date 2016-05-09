<?php

/**
 * Config the column grid system and sets the
 * collection that will be used to recover data.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_Inventory_Block_Adminhtml_Inventory_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Values for filters submitted by the form.
     *
     * @var Varien_Object
     */
    private $filtersData = null;

    /**
     * Starts standards values and calling default constructor parent class.
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('inventoryGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);

        /** @var Reports_Inventory_Helper_Data $helper */
        $helper = Mage::helper('inventory');

        $paramData = $this->getParamData();

        $this->filtersData = $paramData;

        $helper->setFilters($paramData);
    }

    /**
     * Get a filter value of the parameters passed by the form.
     *
     * @param  $key string
     *
     * @return Varien_Object
     */
    public function getFiltersData($key = null)
    {
        return (!is_null($this->filtersData)) ? $this->filtersData->getData($key) : null;
    }

    /**
     * Sets the collection of data to be used in the report and get param data
     * to use in filter collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();

        // Get the data collection from the model
        $this->getCollection()->initReport('inventory/inventory');

        return $this;
    }

    /**
     * Prepare the columns system to display the data.
     *
     * @return $this|void
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('product_register', array(
            'header' => Mage::helper('inventory')->__('Data de Cadastro'),
            'align' => 'left',
            'sortable' => false,
            'index' => 'product_register'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('inventory')->__('SKU'),
            'align' => 'left',
            'sortable' => false,
            'index' => 'sku'
        ));

        $this->addColumn('gender', array(
            'header' => Mage::helper('inventory')->__('Gênero'),
            'align' => 'left',
            'sortable' => false,
            'index' => 'gender'
        ));

        $this->addColumn('category', array(
            'header' => Mage::helper('inventory')->__('Categoria'),
            'align' => 'left',
            'sortable' => false,
            'index' => 'category'
        ));

        $this->addColumn('style', array(
            'header' => Mage::helper('inventory')->__('Estilo'),
            'align' => 'left',
            'sortable' => false,
            'index' => 'style'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventory')->__('Nome do Produto'),
            'align' => 'left',
            'sortable' => false,
            'index' => 'product_name'
        ));

        $this->addColumn('size_p', array(
            'header' => Mage::helper('inventory')->__('P'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'size_p',
        ));

        $this->addColumn('size_M', array(
            'header' => Mage::helper('inventory')->__('M'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'size_M',
        ));

        $this->addColumn('size_G', array(
            'header' => Mage::helper('inventory')->__('G'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'size_G',
        ));

        $this->addColumn('size_EXG', array(
            'header' => Mage::helper('inventory')->__('EXG'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'size_EXG',
        ));

        $this->addColumn('size_EXGG', array(
            'header' => Mage::helper('inventory')->__('EXGG'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'size_EXGG',
        ));

        $this->addColumn('cost', array(
            'header' => Mage::helper('inventory')->__('Custo'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'size_EXGG',
        ));

        $this->addColumn('affiliate_tradicional', array(
            'header' => Mage::helper('inventory')->__('Tradicional'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'affiliate_tradicional',
        ));

        $this->addColumn('affiliate_key', array(
            'header' => Mage::helper('inventory')->__('Key'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'affiliate_key',
        ));

        $this->addColumn('affiliate_morden', array(
            'header' => Mage::helper('inventory')->__('Morden'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'affiliate_morden',
        ));

        $this->addColumn('affiliate_retail', array(
            'header' => Mage::helper('inventory')->__('Varejo'),
            'align' => 'right',
            'sortable' => false,
            'type' => 'number',
            'index' => 'affiliate_retail',
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('inventory')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('inventory')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * @param $from
     * @param $to
     * @return mixed
     */
    public function getReport($from, $to)
    {

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
                $date = new Zend_Date(mktime(0, 0, 0, 1, 1, 2001));
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
        } else if (0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        $filters = new Varien_Object();
        return $filters->setData($data);
    }
}