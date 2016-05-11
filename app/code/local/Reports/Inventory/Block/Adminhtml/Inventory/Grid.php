<?php

/**
 * Config the column grid system and sets the
 * collection that will be used to recover data.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_Inventory_Block_Adminhtml_Inventory_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setCollection(Mage::getModel('inventory/inventory')->getReportData());

        return parent::_prepareCollection();
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
            'filter' => false,
            'index' => 'product_register'
        ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('inventory')->__('SKU'),
            'align' => 'left',
            'sortable' => false,
            'filter' => false,
            'index' => 'sku'
        ));

        $this->addColumn('gender', array(
            'header' => Mage::helper('inventory')->__('Gênero'),
            'align' => 'left',
            'sortable' => false,
            'filter' => false,
            'index' => 'gender',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'


        ));

        $this->addColumn('category', array(
            'header' => Mage::helper('inventory')->__('Categoria'),
            'align' => 'left',
            'sortable' => false,
            'filter' => false,
            'index' => 'category',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'

        ));

        $this->addColumn('style', array(
            'header' => Mage::helper('inventory')->__('Estilo'),
            'align' => 'left',
            'sortable' => false,
            'filter' => false,
            'index' => 'style',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'

        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventory')->__('Nome do Produto'),
            'align' => 'left',
            'sortable' => false,
            'filter' => false,
            'index' => 'product_name'
        ));

        $this->addColumn('size_P', array(
            'header' => Mage::helper('inventory')->__('P'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'size_P',
        ));

        $this->addColumn('size_M', array(
            'header' => Mage::helper('inventory')->__('M'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'size_M',
        ));

        $this->addColumn('size_G', array(
            'header' => Mage::helper('inventory')->__('G'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'size_G',
        ));

        $this->addColumn('size_EXG', array(
            'header' => Mage::helper('inventory')->__('EXG'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'size_EXG',
        ));

        $this->addColumn('size_EXGG', array(
            'header' => Mage::helper('inventory')->__('EXGG'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'size_EXGG',
        ));

        $this->addColumn('cost', array(
            'header' => Mage::helper('inventory')->__('Custo'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'index' => 'cost',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute'
        ));

        $this->addColumn('affiliate_tradicional', array(
            'header' => Mage::helper('inventory')->__('Tradicional'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'affiliate_tradicional',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_SpecialPrice'

        ));

        $this->addColumn('affiliate_key', array(
            'header' => Mage::helper('inventory')->__('Key Account'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'affiliate_key',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_SpecialPrice'

        ));

        $this->addColumn('affiliate_modern', array(
            'header' => Mage::helper('inventory')->__('Modern'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'affiliate_modern',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_SpecialPrice'

        ));

        $this->addColumn('affiliate_retail', array(
            'header' => Mage::helper('inventory')->__('Varejo'),
            'align' => 'right',
            'sortable' => false,
            'filter' => false,
            'type' => 'number',
            'index' => 'affiliate_retail',
            'renderer' => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Money'
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
        return false;//$this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId()));
//        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getProductId()));
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