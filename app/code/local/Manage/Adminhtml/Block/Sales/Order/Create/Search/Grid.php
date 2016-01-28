<?php

/**
 * Adminhtml sales order create search products block
 *
 * @category   Manage
 * @package    Manage_Adminhtml
 * @author     Ronildo dos Santos - Tagon8 Team
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

        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->setStore($this->getStore())
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addStoreFilter()
            ->addAttributeToFilter('type_id', array_keys(
                Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray()
            ))
            ->addAttributeToSelect('gift_message_available');

        //Filter products that don't have 'fr' in SKU
        $collection->getSelect()->where(
            "SUBSTRING(sku, 1, 2) != 'fr'"
        );

        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

}
