<?php

class Magestore_AffiliateplusReferFriend_Block_Product_List
    extends Mage_Catalog_Block_Product_List
{
    public function isEnableShareFriend()
    {
        if ($this->hasData('is_enable_share_friend')) {
            return $this->getData('is_enable_share_friend');
        }
        if (Mage::helper('affiliateplus/account')->accountNotLogin()) {
            $this->setData('is_enable_share_friend', false);
        } else {
            $this->setData('is_enable_share_friend',
                Mage::helper('affiliateplus/config')->getReferConfig('refer_enable_product_list')
            );
        }
        return $this->getData('is_enable_share_friend');
    }
    
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
        $html = parent::getPriceHtml($product, $displayMinimalPrice, $idSuffix);
        if ($this->isEnableShareFriend()) {
            // Add share friend for product list page
            $block = Mage::getBlockSingleton('affiliateplusreferfriend/product_refer');
            $block->setProduct($product);
            $html = $block->toHtml() . $html;
        }
        return $html;
    }

    /**
     * Override the default collection to display the latest products.
     *
     * @return object
     */
    public function getLoadedProductCollection()
    {

        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        $_productCollection = Mage::getModel('catalog/product')->getCollection();
        $_productCollection->addAttributeToSelect('*')
            ->addFieldToFilter('visibility', $visibility) //showing just products visible in catalog or both search and catalog
            ->addFinalPrice()
            ->addAttributeToSort('created_at', 'desc') //in case we would like to sort products by price
            ->getSelect()
            ->limit(30);

        return $_productCollection;
    }

}
