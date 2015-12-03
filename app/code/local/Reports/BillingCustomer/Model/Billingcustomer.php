<?php

/**
 * This class provide a collection data for be showing in report front-end.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_BillingCustomer_Model_Billingcustomer extends Mage_Reports_Model_Mysql4_Product_Ordered_Collection
{
    /**
     * Save value filters.
     *
     * @var array
     */
    private $filters = array();


    public function __construct()
    {
        parent::__construct();

        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('billingcustomer');
        $this->filters = $helper->getFilters();
    }
}