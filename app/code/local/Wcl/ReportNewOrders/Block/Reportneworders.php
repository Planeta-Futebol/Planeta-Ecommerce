<?php

class Wcl_ReportNewOrders_Block_Reportneworders extends Mage_Core_Block_Template
{

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getReportNewOrders() {
        if (!$this->hasData('reportneworders')) {
            $this->setData('reportneworders', Mage::registry('reportneworders'));
        }

        return $this->getData('reportneworders');
    }

}