<?php
class Fuza_Bling_Block_Adminhtml_Bling extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_bling';
		$this->_blockGroup = 'fuza_bling';
		$this->_headerText = 'Bling NFe';

		if (Mage::getStoreConfig('fuza_bling/geral/enable_bling')) {
			$this->_addButton('adminhtml_bling', array(
				'label' => $this->__('Alocar NFe Avulsa'),
				'onclick' => "setLocation('{$this->getUrl('*/*/alocaNfAvulsa')}')",
			));
		}

		parent::__construct();
		
		$this->removeButton('add');
	}//construct

}