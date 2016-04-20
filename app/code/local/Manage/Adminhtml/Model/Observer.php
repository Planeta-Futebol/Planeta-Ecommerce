<?php

class Manage_Adminhtml_Model_Observer
{
    public function getCustomerGrid( $observer )
    {
        $block = $observer->getEvent()->getBlock();

        if ($block->getId() == 'customerGrid') {

            $block->removeColumn('taxvat');
            $block->removeColumn('gender');
            $block->removeColumn('dob');
            $block->removeColumn('age');
            $block->removeColumn('billing_postcode');

            //billing_city

            $block->addColumn( 'account_name', array(
                    'header'    => Mage::helper('customer')->__('Nome do Representante'),
                    'filter'    => false,
                    'index'     => 'account_name'
                )
            );

            $block->addColumn('billing_city',[
                'header'    => Mage::helper('customer')->__('City'),
                'width'     => '100',
                'type'      => 'city',
                'filter'    => false,
                'index'     => 'billing_city',
            ]);

            $block->addColumn('total_purchases',[
                'header'    => Mage::helper('customer')->__('Total de Compras'),
                'width'     => '100',
                'renderer'  => 'manage_adminhtml_block_widget_grid_column_renderer_money',
                'filter'    => false,
                'index'     => 'total_purchases',
            ]);

            $block->addColumn('media_purchases',[
                'header'    => Mage::helper('customer')->__('Média de Compras/Mês'),
                'width'     => '100',
                'renderer'  => 'manage_adminhtml_block_widget_grid_column_renderer_money',
                'filter'    => false,
                'index'     => 'media_purchases',
            ]);

            $block->addColumn('qty_purchases',[
                'header'    => Mage::helper('customer')->__('Qty de Pedidos'),
                'width'     => '100',
                'filter'    => false,
                'index'     => 'qty_purchases',
            ]);

            $block->addColumn('last_purchases',[
                'header'    => Mage::helper('customer')->__('Última Compra'),
                'width'     => '100',
                'filter'    => false,
                'index'     => 'last_purchases'
            ]);

        }
    }

    public function beforeCollectionLoad( $observer )
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }

        /**
         * Mage_Customer_Model_Resource_Customer_Collection
         */
        if ($collection instanceof Mage_Customer_Model_Resource_Customer_Collection) {
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            $collection->getSelect()
                ->columns(
                    [
                        'total_purchases' => '(
                            SELECT SUM(grand_total) FROM sales_flat_order
                            WHERE customer_id = e.entity_id
                        )',

                        'qty_purchases' => '(
                            SELECT COUNT(*) FROM sales_flat_order
                            WHERE customer_id = e.entity_id
                        )',

                        'media_purchases' => '(
                            SELECT
                                ((timestampdiff(day, e.created_at, NOW()))/30) * sum(grand_total)
                            FROM sales_flat_order
                            WHERE customer_id = e.entity_id
                        )',

                        'last_purchases' => '(
                            SELECT DATE_FORMAT(created_at,\'%d-%m-%Y\')
                            FROM sales_flat_order
                            WHERE customer_id = e.entity_id
                            ORDER BY created_at DESC
                            LIMIT 1
                        )',
                    ]
                )
                ->joinLeft(
                    [ 'aft' => 'affiliateplus_transaction'],
                    'aft.customer_id = e.entity_id',
                    ['account_name']
                )->group('e.entity_id');
        }
    }
}