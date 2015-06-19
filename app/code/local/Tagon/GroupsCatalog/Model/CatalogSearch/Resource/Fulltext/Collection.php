<?php


class Tagon_GroupsCatalog_Model_CatalogSearch_Resource_Fulltext_Collection
    extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{
    /**
     * Add the groupscatalog filter to the select object so the number of search
     * results on the pager is correct.
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $helper = Mage::helper('tagon_groupscatalog');
        if ($helper->isModuleActive() && !$helper->isDisabledOnCurrentRoute()) {
            $customerGroupId = $helper->getCustomerGroupId();
            Mage::getResourceSingleton('tagon_groupscatalog/filter')
                    ->addGroupsCatalogFilterToSelectCountSql($select, $customerGroupId, $this->getStoreId());
        }
        return $select;
    }
}
