<?php

class Fuza_Bling_Block_Adminhtml_Bling_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('fuza_bling_grid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('fuza_bling/blingnf')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		//$this->setTemplate('fuza/grid.phtml');

		$this->addColumn('id', array(
			'header'    => 'Nfe',
			'align'     => 'left',
			'index'     => 'id',
            'width' 	=> '80px'
		));

		$this->addColumn('order_id', array(
			'header'    => 'Pedido',
			'align'     => 'left',
			'renderer'  => 'Fuza_Bling_Block_Adminhtml_Bling_Renderer_Link',
            'index' 	=> 'increment_id',
            'width' 	=> '70px',
		));
/*
        $this->addColumn('increment_id', array(
            'header'	=> 'Pedido',
			'align'     => 'left',
            'index' 	=> 'increment_id',
            'width' 	=> '80px'
        ));
        $this->addColumn('link', array(
            'header'	=> 'Pedido',
			'align'     => 'left',
            'width' 	=> '70px',
            'type'      => 'action',
            'getter'    => 'getOrderId',
			'actions'   => array(
                array(
                    'caption'   => 'Visualizar',
                    'url'       => array('base'=> 'adminhtml/sales_order/view'),
                    'target'	=>'_blank',
                    'field'     => 'order_id'
                ),
			),
			'filter'    => false,
			'sortable'  => false
        ));
*/
		$this->addColumn('status', array(
			'header'    => 'Status',
			'index'     => 'status',
            'filter_index' => 'main_table.status',
			'type'      => 'options',
            'width' 	=> '90px',
			'options'   => Mage::getModel('fuza_bling/source_status')->toColumnOptionArray()
		));

		$this->addColumn('status_message', array(
			'header'    => 'Mensagem',
			'align'     => 'left',
			'index'     => 'status_message',
            'width' 	=> '280px',
		));

		$this->addColumn('error_message', array(
			'header'    => 'Erro',
			'align'     => 'left',
			'index'     => 'error_message'
		));

		$this->addColumn('created_at', array(
			'header'    => 'Criada',
			'index'     => 'created_at',
			'type'      => 'datetime',
            'width' 	=> '160px'
		));

		$this->addColumn('updated_at', array(
			'header'    => 'Atualizada',
			'index'     => 'updated_at',
			'type'      => 'datetime',
            'width' 	=> '160px'
		));


		$this->addExportType('*/*/exportCsv', Mage::helper('bling')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('bling')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('fuza_bling');

		return $this;
	}

	public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

	public function getRowUrl($row)
	{
		return false;
	}

}
