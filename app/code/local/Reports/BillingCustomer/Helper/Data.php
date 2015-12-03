<?php

/**
 * This class provide some methods for help in build reports billing customer
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_BillingCustomer_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $filters = array();

    /**
     * Retrieves all filters values subimited by form.
     *
     * @return array
     */
    public function getFilters($key = null)
    {
        return (is_null($key)) ? $this->filters : $this->filters[$key];
    }

    /**
     * Define an array filters to be used by report.
     *
     * @param array $filters
     */
    public function setFilters( array $filters )
    {
        $this->filters = $filters;
    }
}