<?php

class Fuza_Bling_Model_Resource_Blingnf_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	public function _construct(){
        parent::_construct();
		$this->_init('fuza_bling/blingnf', 'id');
	}

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinLeft(array('sfo' => $this->getTable('sales/order')),
                'main_table.order_id = sfo.entity_id',
                array('increment_id'));
        return $this;
    }
}
