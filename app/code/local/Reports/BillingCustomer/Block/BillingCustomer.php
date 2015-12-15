<?php

/**
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_BillingCustomer_Block_BillingCustomer extends Mage_Core_Block_Template
{

    public function _prepareLayout() {

        return parent::_prepareLayout();
    }

    public function getReportBillingCustomer() {
        if (!$this->hasData('billingcustomer')) {
            $this->setData('billingcustomer', Mage::registry('billingcustomer'));
        }

        return $this->getData('billingcustomer');
    }
}