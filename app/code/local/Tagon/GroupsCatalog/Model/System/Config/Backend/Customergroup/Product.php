<?php


class Tagon_GroupsCatalog_Model_System_Config_Backend_Customergroup_Product
    extends Tagon_GroupsCatalog_Model_System_Config_Backend_Customergroup_Abstract
{
    /**
     * Return the indexer code
     *
     * @return string
     * @see Tagon_GroupsCatalog_Model_System_Config_Backend_Customergroup_Abstract::_afterSave()
     */
    protected function _getIndexerCode()
    {
        return 'groupscatalog_product';
    }
}
