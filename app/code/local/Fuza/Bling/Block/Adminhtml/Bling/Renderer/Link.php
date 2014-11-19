<?php

class Fuza_Bling_Block_Adminhtml_Bling_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $text = $row['order_id'];

        if ($text > 0) {
            $increment_id = Mage::getModel('sales/order')->load($text)->getIncrementId();
            $text = '<a href="'.Mage::helper("adminhtml")->getUrl('adminhtml/sales_order/view', array('order_id' => $text)).'" title="Visualizar Pedido" target="_blank">'.$increment_id.'</a>';
        } else {
            $text = "";
        }

        return $text;
    }
}