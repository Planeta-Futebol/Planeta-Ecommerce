<?php

class Wcl_ReportNewOrders_Model_Reportneworders extends Mage_Reports_Model_Mysql4_Product_Ordered_Collection
{

    protected $filters = array();

    function __construct()
    {
        parent::__construct();

        /** @var Wcl_ReportNewOrders_Helper_Data $helper */
        $helper = Mage::helper('reportneworders');

        $this->filters = $helper->getFilters();
    }

    /**
     * Add ordered qty's
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function getReportData( $from = '', $to = '' )
    {

        $adapter = $this->getConnection();
        $orderTableAliasName = $adapter->quoteIdentifier('order');

        $productTableName = Mage::getSingleton('core/resource')->getTableName('catalog/product');

        $orderJoinCondition = array(
                $orderTableAliasName . '.entity_id = order_items.order_id',
                $adapter->quoteInto("{$orderTableAliasName}.state = ?", Mage_Sales_Model_Order::STATE_COMPLETE),
        );

        $productJoinCondition = array(
                'e.entity_id = order_items.product_id',
                $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {
            $fieldName = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }

        $select = $this->getSelect()->reset();

        $select->from(
            array('order_items' => $this->getTable('sales/order_item')),
            array()
        )
                ->columns(array(
                    'type_id'             => 'order_items.product_type',
                    'order_items_name'    => 'order_items.name',
                    'order_increment_id'  => 'order.increment_id',
                    'sku'                 => 'order_items.sku',
                    'qty_ordered'         => 'order_items.qty_ordered',
                    'qty_refunded'        => 'order_items.qty_refunded',
                    'amount_refunded'     => new Zend_Db_Expr(
                        'COALESCE(order_items.amount_refunded, 0) - (
                            (COALESCE(order_items.discount_refunded, 0)) +
                            (
                                (COALESCE(order_items.affiliateplus_amount, 0) / (order_items.row_total - COALESCE(order_items.discount_amount, 0))
                            ) * (COALESCE(order_items.amount_refunded, 0) - COALESCE(order_items.discount_refunded, 0)))
                        )'
                    ),
                    'shipping_address_id' => 'order.shipping_address_id',
                    'total_sold'          => 'order_items.row_total',
                    'discount_amount_store'        => 'order_items.discount_amount',
                    'discount_amount_affiliatplus' => 'order_items.affiliateplus_amount',
                    'discount_amount' => new Zend_Db_Expr(
                        "(COALESCE(`order_items`.`discount_amount`, 0) + COALESCE(`order_items`.`affiliateplus_amount`, 0))"
                    ),
                    'total_liquid'    =>  new Zend_Db_Expr(
                        "`order_items`.`row_total` - (COALESCE(`order_items`.`discount_amount`, 0) + COALESCE(`order_items`.`affiliateplus_amount`, 0))"
                    )
                ))
                ->joinInner(
                        array('order' => $this->getTable('sales/order')),
                        implode(' AND ', $orderJoinCondition),
                        array()
                );

        if(!is_null($this->filters['report_district']) && (int) $this->filters['report_district'] != 0){
            $select = $this->filterByDistrict($select, $orderTableAliasName, $this->filters['report_district']);
        }

        $select = $select->joinLeft(
                array('e' => $this->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                        'created_at' => 'e.created_at',
                        'updated_at' => 'e.updated_at'
                ));


        if (!is_null($this->filters['report_group_products'])) {

            $select = $this->filterByAttibuteProduct(
                    $select,
                    $this->filters['report_group_products'],
                    $this->filters['report_type_products']
            );
        }

        $select->joinLeft(
                        array('p' => $productTableName),
                        'p.sku = order_items.sku',
                        array(
                                'size' => 'COALESCE(p.attribute_set_id, p.attribute_set_id, p.attribute_set_id)'
                        ))
                ->joinLeft(
                        array('av' => 'eav_attribute_option_value'),
                        'p.attribute_set_id = av.option_id',
                        array(
                                'size_label' => 'av.value'
                        ))
                ->where('parent_item_id IS NULL')
                ->order('qty_ordered DESC')
                ->group("DATE_FORMAT(`e`.`created_at`,'%m-%d-%Y'), order_items.sku");

        return $this;
    }

    protected function filterByAttibuteProduct( Varien_Db_Select $select, $attribute, $value )
    {
        $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter( $attribute, $value );

        $key = 0;

        $arrEntityIdsProduct = array();

        foreach($products as $product){
            $arrEntityIdsProduct[$key] = $product['entity_id'];
            $key++;
        }

        $select->where('e.entity_id IN (?)', $arrEntityIdsProduct);

        return $select;
    }

    protected function filterByDistrict( Varien_Db_Select $select, $tableToJoin, $value )
    {
        $connection = $this->getConnection();

        $salesAddressTableAlias = $connection->quoteIdentifier('salesAddress');

        $salesAddressJoinCondition = array(
                $tableToJoin . ".customer_id = {$salesAddressTableAlias}.customer_id",
        );

        $select->join(
                array('salesAddress' => 'sales_flat_order_address'),
                implode($salesAddressJoinCondition),
                array(
                        'region' => 'region_id'
                )
        )->where("{$salesAddressTableAlias}.region_id = ?", array($value));

        return $select;
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

    /**
     * Join fields
     *
     * @param int|string $from
     * @param int|string $to
     * @return Mage_Reports_Model_Resource_Product_Ordered_Collection
     */
    protected function _joinFields( $from = '', $to = '' )
    {

        $this->addAttributeToSelect('name')
                ->addAttributeToSelect('increment_id')
                ->getReportData($from, $to)
                ->setOrder('sku', self::SORT_ORDER_ASC);

        Mage::log('SQL: ' . $this->getSelect()->__toString());

        return $this;
    }

}