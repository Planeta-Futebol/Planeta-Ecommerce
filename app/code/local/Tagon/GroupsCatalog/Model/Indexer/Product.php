<?php


class Tagon_GroupsCatalog_Model_Indexer_Product extends Tagon_GroupsCatalog_Model_Indexer_Abstract
{
    /**
     * For the matched entity and events the _registerEvent() and _processEvent() methods will be called
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        )
    );

    /**
     * Initialize the resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tagon_groupscatalog/indexer_product');
    }

    /**
     * Return the name of the index in the adminhtml
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('tagon_groupscatalog')->__('GroupsCatalog Products');
    }

    /**
     * Return the description of the index in the adminhtml
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('tagon_groupscatalog')->__('Reindex Customergroup Product Visibility');
    }

    /**
     * Return the product ids from a category mass action event
     *
     * @param Varien_Object $entity
     * @return array|null
     * @see Tagon_GroupsCatalog_Model_Indexer_Abstract::_registerEvent()
     */
    protected function _getEntityIdsFromEntity(Varien_Object $entity)
    {
        return $entity->getProductIds();
    }
}
