<?php

/**
 * Created by PhpStorm.
 * User: Ronildo
 * Date: 02/12/15
 * Time: 15:00
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