<?php


abstract class Tagon_GroupsCatalog_Model_System_Config_Backend_Customergroup_Abstract extends Mage_Core_Model_Config_Data
{
    /**
     * Return the indexer code for this backend entity
     *
     * @abstract
     * @return string
     */
    abstract protected function _getIndexerCode();

    /**
     * Disable saving if multiselect fields are disabled
     * 
     * @return $this|Mage_Core_Model_Abstract
     */
    public function save()
    {
        $helper = Mage::helper('tagon_groupscatalog');
        if ($helper->getConfig('show_multiselect_field')) {
            parent::save();
        }
        return $this;
    }
    
    /**
     * Sanitize settings and set the index to require reindex
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (is_array($value) && 1 < count($value)) {
            // if USE_NONE is selected remove all other selected groups
            if (in_array(Tagon_GroupsCatalog_Helper_Data::USE_NONE, $value)) {
                $value = array(Tagon_GroupsCatalog_Helper_Data::USE_NONE);
                $this->setValue($value);
            }
        }
        // Can't use isValueChanged() because it compares string value (old) with array (new)
        $oldValue = explode(',', (string)$this->getOldValue());
        if ($this->getValue() != $oldValue) {
            $indexerCode = $this->_getIndexerCode();
            $process = Mage::getModel('index/indexer')->getProcessByCode($indexerCode);
            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
        return parent::_beforeSave();
    }
}
