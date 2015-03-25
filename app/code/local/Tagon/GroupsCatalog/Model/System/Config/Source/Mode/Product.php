<?php


class Tagon_GroupsCatalog_Model_System_Config_Source_Mode_Product
{
    /**
     * Return the mode options for the product configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('tagon_groupscatalog');
        return array(
            array(
                'value' => Tagon_GroupsCatalog_Helper_Data::MODE_SHOW_BY_DEFAULT,
                'label' => $helper->__('Show products by default')
            ),
            array(
                'value' => Tagon_GroupsCatalog_Helper_Data::MODE_HIDE_BY_DEFAULT,
                'label' => $helper->__('Hide products by default')
            ),
        );
    }
}
