<?php


class Tagon_GroupsCatalog_Model_System_Config_Backend_Reindex_Both
    extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            foreach (array('groupscatalog_category', 'groupscatalog_product') as $indexerCode) {
                $process = Mage::getModel('index/indexer')->getProcessByCode($indexerCode);
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            }
        }
    }
}
