<?php


class Tagon_GroupsCatalog_Model_System_Config_Source_Mode_Category
{
    /**
     * Return the mode options for the category configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('tagon_groupscatalog');
        return array(
            array(
                'value' => Tagon_GroupsCatalog_Helper_Data::MODE_SHOW_BY_DEFAULT,
                'label' => $helper->__('Show categories by default')
            ),
            array(
                'value' => Tagon_GroupsCatalog_Helper_Data::MODE_HIDE_BY_DEFAULT,
                'label' => $helper->__('Hide categories by default')
            ),
        );
    }
}
