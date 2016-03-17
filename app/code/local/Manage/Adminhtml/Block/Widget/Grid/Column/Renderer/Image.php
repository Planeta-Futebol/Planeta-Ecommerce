<?php

/**
 * This class provides a way to show images products.
 * The image configurable product is show in all your child.
 * The configurable product is recovered by child id.
 *
 * @category   Manage
 * @package    Manage_Adminhtml
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Adminhtml_Block_Widget_Grid_Column_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render( Varien_Object $row )
    {
        $productId =  $row->getData($this->getColumn()->getIndex());

        // recover the configurable id product by your child id
        $parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')
            ->getParentIdsByChild($productId);

        $product = Mage::getModel('catalog/product')->load($parentIds);

        $productMediaConfig = Mage::getModel('catalog/product_media_config');

        // absolute path to image product
        $baseImageUrl = $productMediaConfig->getMediaUrl($product->getImage());

        $value = 'Sem imagem';

        if($product->getImage() && $product->getImage() != 'no_selection')
        {
            $value ='<img src="' . $baseImageUrl . '" width="100" height="100" />';

        }

        return $value;
    }
}
