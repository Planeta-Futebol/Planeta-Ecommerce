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

    /**
     * Prepares a collection of data that will be displayed in the final report on the front end
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function getReportData( $from = '', $to = '' )
    {
        $adapter = $this->getConnection();
        $orderTableAliasName = $adapter->quoteIdentifier('order');

        $orderJoinCondition = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state = ?", Mage_Sales_Model_Order::STATE_COMPLETE),
        );

        $productJoinCondition = array(
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        $salesAddressTableAlias = $adapter->quoteIdentifier('salesAddress');

        $salesAddressJoinCondition = array(
            $orderTableAliasName . ".customer_id = {$salesAddressTableAlias}.customer_id",
        );

        $couponJoinCondition = array(
            $orderTableAliasName . '.affiliateplus_coupon = c.coupon_code'
        );

        if ($from != '' && $to != '') {
            $fieldName = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }

        $select = $this->getSelect()->reset();

        $select->from(array('order_items' => $this->getTable('sales/order_item')))
            ->columns(array(
                'full_name_cutomer' => "CONCAT({$orderTableAliasName}.customer_firstname, ' ', {$orderTableAliasName}.customer_lastname)"
            ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array()
            )->joinLeft(
                array('e' => $this->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                )
            )->joinLeft(
                array('salesAddress' => 'sales_flat_order_address'),
                implode($salesAddressJoinCondition),
                array(
                    'state' => 'region'
                )
            )
            ->joinLeft(
                array('c' => 'affiliateplus_coupon'),
                implode($couponJoinCondition),
                array(
                    'representative_name' => 'IF(c.account_name IS NOT NULL, c.account_name, "Não possui representante" )'
                )
            )
            ->joinLeft(
                array('pro' => 'affiliateplusprogram'),
                'c.program_id = pro.program_id',
                array()
            )
            ->joinLeft(
                array('usage_coupon' => 'salesrule_coupon_usage'),
                "{$orderTableAliasName}.customer_id = usage_coupon.customer_id",
                array()
            )
            ->joinLeft(
                array('coupon' => 'salesrule_coupon'),
                "coupon.coupon_id = usage_coupon.coupon_id",
                array()
            )
            ->joinLeft(
                array('rule' => 'salesrule'),
                "rule.rule_id = coupon.rule_id",
                array(
                    'discount_amount' => new Zend_Db_Expr("
                            IF(coupon.rule_id IS NULL,
                                ((order_items.price * SUM(order_items.qty_ordered)) * (pro.discount / 100)),
                                ((order_items.price * SUM(order_items.qty_ordered)) * (rule.discount_amount / 100))
                            )"
                    ),
                )
            );

        Mage::log((string) $select, null, 'billingcustomer');

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
        
        return $this;
    }
}