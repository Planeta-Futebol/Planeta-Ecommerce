<?php

/**
 * Config the column grid system and sets the
 * collection that will be used to recover data.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer_Block_Adminhtml_BillingCustomer_Grid
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

    /**
     * Prepare the columns system to display the data.
     *
     * @return $this|void
     * @throws Exception
     */
    protected function _prepareColumns() {

        $this->addColumn('custome_name', array(
            'header'   => Mage::helper('reportneworders')->__('Nome do Cliente'),
            'align'    => 'left',
            'sortable' => false,
            'index'    => 'custome_name'
        ));

        $this->addColumn('state', array(
            'header'   => Mage::helper('reportneworders')->__('Estado'),
            'align'    => 'left',
            'sortable' => false,
            'index'    => 'state'
        ));

        $this->addColumn('representative_name', array(
            'header'   => Mage::helper('reportneworders')->__('Nome do Representante'),
            'align'    => 'left',
            'sortable' => false,
            'index'    => 'state'
        ));

        $this->addColumn('qty_order', array(
            'header'    => Mage::helper('reports')->__('Quantidade de Pedidos'),
            'align'     =>'right',
            'sortable'  => false,
            'type'      =>'number',
            'index'     => 'qty_order'
        ));

        $this->addColumn('amount_refunded', array(
            'header'    => Mage::helper('reportneworders')->__('Valor devolvido'),
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'amount_refunded'
        ));

        $this->addColumn('total_sold', array(
            'header'    => Mage::helper('reportneworders')->__('Valor de Compras'),
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'total_sold'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('reportneworders')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('reportneworders')->__('XML'));

        return parent::_prepareColumns();
    }
}