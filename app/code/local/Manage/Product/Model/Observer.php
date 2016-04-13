<?php
/**
 * This Observer get catalog_product_save_commit_after event.
 *
 * @category   Manage
 * @package    Manage_Redirect
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Product_Model_Observer
{

    /**
     * This method captures the price of the configurable product
     * and its price groups and applies in all its products children.
     *
     * @param $observer
     */
    public function setProductPriceByConfigurable($observer )
    {
        $product = $observer->getEvent()->getProduct();

        if( $product->getTypeId() == 'configurable' ){

            $childProducts = Mage::getModel('catalog/product_type_configurable')
                ->getUsedProducts(null, $product);

            foreach($childProducts as $child) {

                // this prevent constraint violation mysql
                $child->setData('group_price', array());
                $child->save();

                $child->setData('group_price', $product->getData('group_price'));
                $child->setPrice($product->getPrice());
                $child->save();
            }
        }
    }
}