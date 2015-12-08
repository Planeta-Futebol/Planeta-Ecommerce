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

        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $url = Mage::getSingleton('core/url')->parseUrl($currentUrl);
        $path = $url->getPath();

        if($path == '/novidades'){

            $select = $this->_productCollection->getSelect()
                ->reset(Zend_Db_Select::WHERE)
                ->reset(Zend_Db_Select::LIMIT_COUNT)
                ->limit(30);

            $this->_productCollection->clear();

        }
            return $this->_getProductCollection();
    }
}
