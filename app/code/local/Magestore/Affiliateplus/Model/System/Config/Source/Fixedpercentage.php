<?php

class Magestore_Affiliateplus_Model_System_Config_Source_Fixedpercentage
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'fixed', 'label'=>Mage::helper('affiliateplus')->__('Fixed Amount')),
            array('value' => 'percentage', 'label'=>Mage::helper('affiliateplus')->__('Percentage')),
            array('value' => 'cart_fixed', 'label'=>Mage::helper('affiliateplus')->__('Fixed amount for whole cart (Commission On Cart mode)')),    // Changed By Adam 22/07/2014
        );
    }

}