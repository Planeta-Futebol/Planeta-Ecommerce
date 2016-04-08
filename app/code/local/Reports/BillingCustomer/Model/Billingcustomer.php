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
    private $filters = null;


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

        $statusOrderCompleted = Mage_Sales_Model_Order::STATE_COMPLETE;
        $statusOrderCanceled  = Mage_Sales_Model_Order::STATE_CANCELED;
        $statusOrderClosed    = Mage_Sales_Model_Order::STATE_CLOSED;

        $orderTableAliasName = $adapter->quoteIdentifier('order');

        $orderJoinCondition = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
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

        $betweenDateSalesOrderItem       = $this->_prepareBetweenSql('sales_order_item' . '.created_at', $from, $to);
        $betweenDateSalesOrderCanceled   = $this->_prepareBetweenSql('sales_canceled' . '.created_at', $from, $to);
        $betweenDateSalesOrderClosed     = $this->_prepareBetweenSql('sales_closed' . '.created_at', $from, $to);
        $betweenDateSalesOrderAmount     = $this->_prepareBetweenSql('sales_amount' . '.created_at', $from, $to);
        $betweenDateSalesOrderRefunded   = $this->_prepareBetweenSql('sales_refunded' . '.created_at', $from, $to);
        $betweenDateSalesOrderDiscount   = $this->_prepareBetweenSql('sales_discount' . '.created_at', $from, $to);
        $betweenDateSalesOrderShipping   = $this->_prepareBetweenSql('sales_shipping' . '.created_at', $from, $to);

        $select = $this->getSelect()->reset();

        $select->from(array('order_items' => $this->getTable('sales/order_item')), array())
            ->columns(array(
                'order_id' => 'order.entity_id',
                'date_customer_register' => 'customer.created_at',
                'group_cutomer'          => 'c_group.customer_group_code',
                'full_name_cutomer'      => "CONCAT({$orderTableAliasName}.customer_firstname, ' ', {$orderTableAliasName}.customer_lastname)",
                'customer_email'         => 'order.customer_email',
                'affiliateplus_coupon'   => 'IF(c.coupon_code  IS NOT NULL, c.coupon_code , "Não Possui Código de Afiliado")',

                'qty_products_sold'      => "(
                    SELECT SUM(total_item_count) FROM sales_flat_order as sales_order_item
                    where sales_order_item.customer_id = order.customer_id
                        and {$betweenDateSalesOrderItem}
                        and (
                            sales_order_item.state = '{$statusOrderCompleted}' OR
                            sales_order_item.state = '{$statusOrderCanceled}' OR
                            sales_order_item.state = '{$statusOrderClosed}'
                        )
                )",

                'qty_order'              => 'count(distinct `order`.entity_id)',

                'qty_order_canceled'     => "(
                    SELECT COUNT(*) FROM sales_flat_order as sales_canceled
                    where status='canceled' and sales_canceled.customer_id = order.customer_id
                        and {$betweenDateSalesOrderCanceled}
                )",

                'qty_order_closed'      => "(
                    SELECT COUNT(total_refunded) FROM sales_flat_order as sales_closed
                    where sales_closed.customer_id = order.customer_id
                        and {$betweenDateSalesOrderClosed}
                        and (
                            sales_closed.state = '{$statusOrderCompleted}' OR
                            sales_closed.state = '{$statusOrderCanceled}' OR
                            sales_closed.state = '{$statusOrderClosed}'
                        )
                )",

                'total_sold'            => "(
                    SELECT SUM(COALESCE(grand_total, 0)) FROM sales_flat_order as sales_amount
                    where sales_amount.customer_id = order.customer_id
                        and {$betweenDateSalesOrderAmount}
                        and (
                            sales_amount.state = '{$statusOrderCompleted}' OR
                            sales_amount.state = '{$statusOrderCanceled}' OR
                            sales_amount.state = '{$statusOrderClosed}'
                        )

                )",

                'total_invoiced'            => "(
                    SELECT SUM(COALESCE(grand_total, 0)) FROM sales_flat_order as sales_amount
                    where sales_amount.customer_id = order.customer_id
                        and {$betweenDateSalesOrderAmount}
                        and (
                            sales_amount.state = '{$statusOrderCompleted}' OR
                            sales_amount.state = '{$statusOrderCanceled}' OR
                            sales_amount.state = '{$statusOrderClosed}'
                        )

                )",

                'total_order_canceled'  => "(
                    SELECT SUM(COALESCE(total_canceled, 0)) FROM sales_flat_order as sales_canceled
                    where status='canceled'
                        and sales_canceled.customer_id = order.customer_id
                        and {$betweenDateSalesOrderCanceled}
                )",
                'total_amount_refunded' => "(
                	SELECT SUM(COALESCE(total_refunded, 0)) FROM sales_flat_order as sales_refunded
                    where sales_refunded.customer_id = order.customer_id
                        and {$betweenDateSalesOrderRefunded}
                        and (
                            sales_refunded.state = '{$statusOrderCompleted}' OR
                            sales_refunded.state = '{$statusOrderCanceled}' OR
                            sales_refunded.state = '{$statusOrderClosed}'
                        )
                )",

                'dicount_amount'       => "(
                    SELECT (SUM(COALESCE(discount_amount, 0)) + (SUM(COALESCE(affiliateplus_discount,0)))) * -1 FROM sales_flat_order as sales_discount
                    where sales_discount.customer_id = order.customer_id
                        and {$betweenDateSalesOrderDiscount}
                        and (
                            sales_discount.state = '{$statusOrderCompleted}' OR
                            sales_discount.state = '{$statusOrderCanceled}' OR
                            sales_discount.state = '{$statusOrderClosed}'
                        )

                )",

                'shipping_amount'      => "(
                    SELECT SUM(COALESCE(shipping_amount, 0)) FROM sales_flat_order as sales_shipping
                    where sales_shipping.customer_id = order.customer_id
                        and {$betweenDateSalesOrderShipping}
                        and (
                            sales_shipping.state = '{$statusOrderCompleted}' OR
                            sales_shipping.state = '{$statusOrderCanceled}' OR
                            sales_shipping.state = '{$statusOrderClosed}'
                        )
                )"
            ))
            ->joinInner(
                array('order' => $this->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array()
            )->joinInner(
                array('customer' => 'customer_entity'),
                'order.customer_id = customer.entity_id',
                array()
            )->joinInner(
                array('c_group' => 'customer_group'),
                'customer.group_id = c_group.customer_group_id',
                array()
            )
            ->joinLeft(
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
                array('aft' => 'affiliateplus_transaction'),
                'aft.order_id = `order`.entity_id',
                array(
                    'representative_name' => 'IF(aft.account_name IS NOT NULL, aft.account_name, "Não possui representante" )'
                )
            )
            ->joinLeft(
                array('c' => 'affiliateplus_coupon'),
                "aft.account_id = c.account_id AND aft.program_id = c.program_id",
                array()
            )->where("
                {$orderTableAliasName}.state = '{$statusOrderCompleted}' OR
                {$orderTableAliasName}.state = '{$statusOrderCanceled}' OR
                {$orderTableAliasName}.state = '{$statusOrderClosed}'
            ")

            ->group('order.customer_id');

        Mage::log((string) $select, null, 'billingcustomer');

        /** @var Reports_BillingCustomer_Helper_Data $helper */
        $helper = Mage::helper('billingcustomer');
        $helper->setCollection($this);

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