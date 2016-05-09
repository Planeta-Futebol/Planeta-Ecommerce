<?php

/**
 * This class provide some methods for help in build reports billing customer
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_Inventory_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Varien_Object
     */
    private $filters = null;

    /**
     * @var Mage_Reports_Model_Resource_Product_Collection
     */
    private $collection;

    /**
     * Retrieves all filters values subimited by form.
     *
     * @param $key string
     * @return Varien_Object
     */
    public function getFilters( $key = null )
    {
        return (is_null($key)) ? $this->filters : $this->filters->getData($key);
    }

    /**
     * Define filters to be used by report.
     *
     * @param Varien_Object $filters
     */
    public function setFilters( Varien_Object $filters )
    {
        $this->filters = $filters;
    }

    /**
     * Define the collection that will be used for calculate subtotal values and grand total values.
     *
     * @param Mage_Reports_Model_Resource_Product_Collection $collection
     */
    public function setCollection( $collection )
    {
        $this->collection = $collection;
    }

    /**
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}