<?php

class Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $productId =  $row->getData($this->getColumn()->getIndex());
        $product = Mage::getModel('catalog/product')->load($productId);

        $value = 'Sem imagem';
        if($product->getImage() && $product->getImage() != 'no_selection')
        {
            $value='<img src="' . $product->getImageUrl() . '" width="100" height="100" />';
        }

        return $value;

    }
}