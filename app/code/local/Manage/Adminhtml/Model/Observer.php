<?php
/**
 * This Observer listens core abstract events.
 *
 * @category   Manage
 * @package    Manage_Adminhtml
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Adminhtml_Model_Observer
{
    /**
     * This methode are litening adminhtml_block_html_before
     * to remove fields thate was added for others blocks.
     * This methode adds new fields in Customer Grid.
     *
     * @param Varien_Event_Observer $observer
     */
    public function getCustomerGrid( Varien_Event_Observer $observer )
    {
        $block = $observer->getEvent()->getBlock();

        if ($block->getId() == 'customerGrid') {

            $block->removeColumn('taxvat');
            $block->removeColumn('gender');
            $block->removeColumn('dob');
            $block->removeColumn('age');
            $block->removeColumn('billing_postcode');

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

            $block->addColumn('credit_limit',[
                'header'    => Mage::helper('customer')->__('Limite de Crédito'),
                'width'     => '100',
                'filter'    => false,
                'renderer'  => 'manage_adminhtml_block_widget_grid_column_renderer_money',
                'index'     => 'credit_limit'
            ]);

            $block->addColumn('open_value',[
                'header'    => Mage::helper('customer')->__('Valor em Aberto'),
                'width'     => '100',
                'filter'    => false,
                'renderer'  => 'manage_adminhtml_block_widget_grid_column_renderer_money',
                'index'     => 'open_value'
            ]);

            $block->addColumn('has_stande',[
                'header'    => Mage::helper('customer')->__('Tem Stande'),
                'width'     => '100',
                'filter'    => false,
                'index'     => 'has_stande'
            ]);


            $block->addColumn('payment_range',[
                'header'    => Mage::helper('customer')->__('Intervalo de Pagamentos'),
                'width'     => '100',
                'filter'    => false,
                'index'     => 'payment_range'
            ]);

        }
    }
}