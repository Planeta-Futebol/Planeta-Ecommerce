<?php

/**
 * Adminhtml sales order create search products block.
 *
 * This class overwrite the main class in the product list in a new purchase order made by adimin.
 * It is used to remove the products that have collection "fr" in the sku.
 * It is used to display the franchisee group values if the User are a franchisee.
 *
 * @category   Manage
 * @package    Manage_Adminhtml
 * @author     Ronildo dos Santos - Tagon8 Developer Team
 */
class Manage_Adminhtml_Block_Sales_Order_Create_Search_Grid extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('manage_adminhtml_block_sales_order_create_search_grid');
    }

    public function setCollection($collection){
        $group_id = Mage::getSingleton('adminhtml/session_quote')->getCustomer()->getGroup_id();

        //prevente error in mysql query
        $group_id = (is_null($group_id)) ? 0 : $group_id;

        $collection
            ->addExpressionAttributeToSelect('neq_sku', 'SUBSTRING({{sku}}, 1, 2)', 'sku')
            ->addAttributeToFilter('type_id', array(
                'neq' => 'configurable'
            ))
            ->addAttributeToFilter('neq_sku', array(
                'neq' => 'fr'
            ));

        // Recover price of affiliate group
        $s = $collection->getSelect()
            ->joinLeft(
                array('c' => 'catalog_product_entity_group_price'),
                "e.entity_id = c.entity_id and c.customer_group_id = {$group_id}",
                array(
                    'affiliate_value' => 'COALESCE(c.value, 0)'
                )
            )->join(
                array('s' => 'cataloginventory_stock_item'),
                "e.entity_id = s.item_id and s.qty > 0",
                array(
                    'quantity' => 'FORMAT(s.qty, 0)'
                )
            );

        Mage::log( (String) $s, null, 'produtos');

        parent::setCollection($collection);
    }


    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {

        Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('sales')->__('Imagem do produto'),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'entity_id',
            'renderer'  => 'Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Image'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_product',
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '80',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
        ));

        $this->addColumn('affiliate_value', array(
            'header'    => Mage::helper('sales')->__('Preço de Afiliado'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'affiliate_value',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
        ));

        $this->addColumn('quantity', array(
            'filter'    => false,
            'sortable'  => false,
            'header'    => Mage::helper('sales')->__('Quantidade'),
            'index'     => 'quantity',
            'align'     => 'center',
        ));

        $this->addColumn('in_products', array(
            'header'    => Mage::helper('sales')->__('Select'),
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'sortable'  => false,
        ));

        $this->addColumn('qty', array(
            'filter'    => false,
            'sortable'  => false,
            'header'    => Mage::helper('sales')->__('Qty To Add'),
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'center',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
        ));
    }
}
