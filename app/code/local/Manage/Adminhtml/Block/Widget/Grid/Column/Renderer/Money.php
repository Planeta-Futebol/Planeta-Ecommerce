<?php

/**
 *
 * @category   Manage
 * @package    Manage_Adminhtml
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Money
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render( Varien_Object $row )
    {
        $value =  $row->getData($this->getColumn()->getIndex());

        $price = Mage::getModel('directory/currency')->format(
            $value,
            array('display'=>Zend_Currency::NO_SYMBOL),
            false
        );

        return $price;
    }
}
