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
    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var Mage_Reports_Model_Resource_Product_Collection
     */
    private $collection;

    private $key = 0;

    /**
     * @var array
     */
    private $subTotals = array();

    /**
     * @return array
     */
    public function getSubTotals()

    {
        return $this->subTotals;
    }

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

    /**
     * @return Varien_Object
     */
    public function getTotals()
    {

        $totals = new Varien_Object();
        $fields = array(
            'qty_order' => 0, //actual column index, see _prepareColumns()
            'total_amount_refunded' => 0,
            'total_sold' => 0,
        );
        foreach ($this->collection as $item) {
            foreach($fields as $field=>$value){
                $fields[$field]+=$item->getData($field);
            }
        }

        $totals->setData($fields);

        if($this->key++%5 == 0) {
            $this->subTotals[] = $fields;
        }
        return $totals;
    }

    /**
     * @param Mage_Reports_Model_Resource_Product_Collection $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }
}