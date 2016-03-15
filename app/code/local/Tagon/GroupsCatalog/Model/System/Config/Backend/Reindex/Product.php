<?php


class Tagon_GroupsCatalog_Model_System_Config_Backend_Reindex_Product
    extends Tagon_GroupsCatalog_Model_System_Config_Backend_Reindex_Abstract
{
    /**
     * Return the indexer code
     *
     * @return string
     * @see Tagon_GroupsCatalog_Model_System_Config_Backend_Mode_Abstract::_afterSave()
     */
    protected function _getIndexerCode()
    {
        return 'groupscatalog_product';
    }
}
