<?php

/**
 * This class provide a collection data for be showing in report front-end.
 *
 * @category   Reports
 * @package    Reports_BillingCustomer
 * @author     Ronildo dos Santos
 */
class Reports_Inventory_Model_Inventory extends Mage_Reports_Model_Mysql4_Product_Collection
{
    /**
     * Save value filters.
     *
     * @var array
     */
    private $filters = null;


    public function __construct()
    {

        parent::__construct();

        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('inventory');
        $this->filters = $helper->getFilters();
    }

    public function getReportData()
    {

        $select = $this->getSelect()->reset();

        $select->from(array('stock_item' => $this->getTable('cataloginventory/stock_item')))
            ->columns([
                'size_P' => '
                    (
                        SELECT st.qty FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-P")
                    )
                ',
                'size_M' => '
                    (
                        SELECT st.qty FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-M")
                    )
                ',
                'size_G' => '
                    (
                        SELECT st.qty FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-G")
                    )
                ',
                'size_EXG' => '
                    (
                        SELECT st.qty FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-EXG")
                    )
                ',
                'size_EXGG' => '
                    (
                        SELECT st.qty FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-EXGG")
                    )
                ',
                'affiliate_retail' => 'e.price',
                'affiliate_tradicional' => 'CONCAT(e.entity_id, "-traditional trade")',
                'affiliate_key' => 'CONCAT(e.entity_id, "-Key Account")',
                'affiliate_modern' => 'CONCAT(e.entity_id, "-Mordern Trade")',
                'cost' => 'CONCAT(e.entity_id, "-cost")',
                'style' => 'CONCAT(e.entity_id, "-style")',
                'gender' => 'CONCAT(e.entity_id, "-genero")',
                'category' => 'CONCAT(e.entity_id, "-categoria")'
            ])->join(
                ['e' => 'catalog_product_flat_1'],
                'e.entity_id = stock_item.product_id',
                [
                    'product_register' => 'e.created_at',
                    'product_name' => 'e.name',
                    'sku' => 'e.sku'
                ]
            )->where('e.type_id = "configurable"')
            ->order('e.created_at DESC')
        ;
        return $this;
    }


    /**
     * Adding item to item array
     *
     * @param   Varien_Object $item
     * @return  Varien_Data_Collection
     */
    public function addItem( Varien_Object $item )
    {
        $itemId = $this->_getItemId($item);

        if ( !is_null($itemId)) {
            $this->_items[$itemId] = $item;
        } else {
            $this->_items[] = $item;
        }
        return $this;
    }
}