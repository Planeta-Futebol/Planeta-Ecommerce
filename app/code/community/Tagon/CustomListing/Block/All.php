<?php

class Tagon_CustomListing_Block_All extends Tagon_CustomListing_Block_Abstract
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getResourceModel('reports/product_collection');
            $this->_productCollection->addAttributeToSelect('*')
                                     ->addStoreFilter();
        }

        return parent::_getProductCollection();
    }
}