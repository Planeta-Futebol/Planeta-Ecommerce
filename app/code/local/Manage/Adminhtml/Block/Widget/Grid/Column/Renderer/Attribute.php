<?php

/**
 *
 * @category   Manage
 * @package    Manage_Adminhtml
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Attribute
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render( Varien_Object $row )
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        $value = explode('-', $value);
        $productId    = $value[0];
        $attibuteCode = $value[1];
        $product = Mage::getModel('catalog/product')->load($productId);

        return $product->getResource()->getAttribute($attibuteCode)->getFrontend()->getValue($product);
    }
}
