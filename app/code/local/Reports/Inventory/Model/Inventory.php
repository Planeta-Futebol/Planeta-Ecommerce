<?php

/**
 * This class provide a collection data for be showing in report front-end.
 * This data collection is the report of inventory of shirts and T-shirts mainly,
 * but includes the filter every product that is configurable.
 *
 * @category   Reports
 * @package    Reports_Inventory
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
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

        $helper = Mage::helper('inventory');
        $this->filters = $helper->getFilters();
    }

    /**
     * This method is called to mount the report of stock, filtering attributes of the product,
     * and selecting mostly configurable products.
     *
     * @return $this
     */
    public function getReportData()
    {

        $select = $this->getSelect()->reset();

        $select->from(array('stock_item' => $this->getTable('cataloginventory/stock_item')))
            ->columns([
                'size_P' => '
                    (
                        SELECT sum(st.qty) FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-P")
                        OR c.sku = CONCAT(e.sku, "-2 anos")
                        OR c.sku = CONCAT(e.sku, "-38")
                    )
                ',
                'size_M' => '
                    (
                        SELECT sum(st.qty) FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-M")
                        OR c.sku = CONCAT(e.sku, "-4 anos")
                        OR c.sku = CONCAT(e.sku, "-40")

                    )
                ',
                'size_G' => '
                    (
                        SELECT sum(st.qty) FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-G")
                        OR c.sku = CONCAT(e.sku, "-6 anos")
                        OR c.sku = CONCAT(e.sku, "-42")
                    )
                ',
                'size_GG' => '
                    (
                        SELECT sum(st.qty) FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-GG")
                        OR c.sku = CONCAT(e.sku, "-8 anos")
                        OR c.sku = CONCAT(e.sku, "-44")
                    )
                ',
                'size_EXG' => '
                    (
                        SELECT (st.qty) FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-EXG")
                        OR c.sku = CONCAT(e.sku, "-10 anos")
                        OR c.sku = CONCAT(e.sku, "-46")
                    )
                ',
                'size_EXGG' => '
                    (
                        SELECT st.qty FROM cataloginventory_stock_item st
                        INNER JOIN catalog_product_flat_1 c
                        ON st.product_id = c.entity_id
                        WHERE c.sku = CONCAT(e.sku, "-EXGG")
                        OR c.sku = CONCAT(e.sku, "-48")
                    )
                ',
                'affiliate_retail'      => 'e.price',
                'affiliate_tradicional' => 'CONCAT(e.entity_id, "-traditional trade")',
                'affiliate_key'         => 'CONCAT(e.entity_id, "-Key Account")',
                'affiliate_modern'      => 'CONCAT(e.entity_id, "-Mordern Trade")',
                'cost'     => 'CONCAT(e.entity_id, "-cost")',
                'style'    => 'CONCAT(e.entity_id, "-style")',
                'gender'   => 'CONCAT(e.entity_id, "-genero")',
                'category' => 'CONCAT(e.entity_id, "-categoria")',
                'clothing' => 'CONCAT(e.entity_id, "-vestuario")'
            ])->join(
                ['e' => 'catalog_product_flat_1'],
                'e.entity_id = stock_item.product_id',
                [
                    'product_register' => 'e.created_at',
                    'product_name' => 'e.name',
                    'sku' => 'e.sku'
                ]
            )->where('e.type_id = "configurable"')
            ->order('e.created_at DESC');

        return $this;
    }
}