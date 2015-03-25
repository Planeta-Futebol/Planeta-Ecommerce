<?php


class Tagon_GroupsCatalog_Model_System_Config_Source_HiddenEntityHandling
{
    const HIDDEN_ENTITY_HANDLING_NOROUTE = '404';
    const HIDDEN_ENTITY_HANDLING_REDIRECT = '302';
    const HIDDEN_ENTITY_HANDLING_REDIRECT_PARENT = '302-parent';

    public function toOptionArray()
    {
        $helper = Mage::helper('tagon_groupscatalog');
        return array(
            array(
                'value' => self::HIDDEN_ENTITY_HANDLING_NOROUTE,
                'label' => $helper->__('Show 404 Page')
            ),
            array(
                'value' => self::HIDDEN_ENTITY_HANDLING_REDIRECT,
                'label' => $helper->__('Redirect to target route')
            ),
            array(
                'value' => self::HIDDEN_ENTITY_HANDLING_REDIRECT_PARENT,
                'label' => $helper->__('Redirect to parent directory')
            )
        );
    }
}
