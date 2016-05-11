<?php

/**
 *
 * @category   Manage
 * @package    Manage_Adminhtml
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_SpecialPrice
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render( Varien_Object $row )
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        $value = explode('-', $value);
        $productId     = $value[0];
        $affiliateType = $value[1];

        $product = Mage::getModel('catalog/product')->load($productId);
        $groupPrice = $product->getData('group_price');
        $groupId = 0;

        if( $product->getTypeId() == 'configurable' ){

            $collectionGroup = Mage::getModel('customer/group')->getCollection();

            foreach($collectionGroup as $group){
                if($group->getData('customer_group_code') == $affiliateType){
                    $groupId = $group->getData('customer_group_id');
                    break;
                }
            }
        }

        $price = Mage::getModel('directory/currency')->format(
            $groupPrice[$groupId]['price'],
            array('display'=>Zend_Currency::NO_SYMBOL),
            false
        );

        return $price;
    }
}
