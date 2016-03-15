<?php
class Devinc_Ajaxcartx_Block_Product_List_Staticblock extends Mage_Catalog_Block_Product_List_Upsell
{    
    public function getAdditionalContentType() {
    	return 'staticblock';
    }

    public function getAdditionalContent() {
    	return $this->getLayout()->createBlock('cms/block')->setBlockId(Mage::getStoreConfig('ajaxcartx/popup_configuration/additional_content'))->toHtml();;
    }

}