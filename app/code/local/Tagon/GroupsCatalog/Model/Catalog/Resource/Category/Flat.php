<?php


class Tagon_GroupsCatalog_Model_Catalog_Resource_Category_Flat
    extends Mage_Catalog_Model_Resource_Category_Flat
{
    /**
     * We need to rewrite this class to be able to filter hidden categories if the
     * flat catalog category is enabled.
     * 
     * @param Mage_Catalog_Model_Category|int $parentNode
     * @param integer $recursionLevel
     * @param integer $storeId
     * @param bool $onlyActive
     * @return Mage_Catalog_Model_Resource_Category_Flat
     */
    protected function _loadNodes($parentNode = null, $recursionLevel = 0, $storeId = 0, $onlyActive = true)
    {
        $nodes = parent::_loadNodes($parentNode, $recursionLevel, $storeId, $onlyActive);

        /* @var $helper Tagon_GroupsCatalog_Helper_Data */
        $helper = Mage::helper('tagon_groupscatalog');
        if ($helper->isModuleActive() && !$helper->isDisabledOnCurrentRoute()) {
            // Filter out hidden nodes
            if (count($nodes) > 0) {
                $nodeIds = array_keys($nodes);
                $visibleIds = Mage::getResourceSingleton('tagon_groupscatalog/filter')
                        ->getVisibleIdsFromEntityIdList(
                            Mage_Catalog_Model_Category::ENTITY, $nodeIds, $storeId, $helper->getCustomerGroupId()
                        );
                $nodes = array_intersect_key($nodes, array_flip($visibleIds));
            }
        }
        return $nodes;
    }
}
