<?php

class Manage_Adminhtml_Block_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    public function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->removeColumn('gender');
        $this->removeColumn('taxvat');
    }
}