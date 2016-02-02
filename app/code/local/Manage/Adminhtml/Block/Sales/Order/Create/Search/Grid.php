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

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {

        parent::_prepareCollection();

        $group_id = Mage::getSingleton('adminhtml/session_quote')->getCustomer()->getGroup_id();

        //prevente error in mysql query
        $group_id = (is_null($group_id)) ? 0 : $group_id;

        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();

        $collection
            ->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addExpressionAttributeToSelect('neq_sku', 'SUBSTRING({{sku}}, 1, 2)', 'sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', array(
                'neq' => 'configurable'
            ))
            ->addAttributeToFilter('neq_sku', array(
                'neq' => 'fr'
            ));

        // Recover price of affiliate group
        $collection->getSelect()
            ->joinLeft(
                array('c' => 'catalog_product_entity_group_price'),
                "e.entity_id = c.entity_id and c.customer_group_id = {$group_id}",
                array(
                    'special_price' => 'COALESCE(c.value, 10)'
                )
            );

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
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
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id'
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

        $this->addColumn('special_price', array(
            'header'    => Mage::helper('sales')->__('Preço de Afiliado'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'special_price',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
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
