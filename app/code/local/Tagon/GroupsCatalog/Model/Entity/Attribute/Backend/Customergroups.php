<?php


class Tagon_GroupsCatalog_Model_Entity_Attribute_Backend_Customergroups
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Process the attribute value before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);
        $customerGroupIds = array(
            Tagon_GroupsCatalog_Helper_Data::USE_DEFAULT,
            Tagon_GroupsCatalog_Helper_Data::USE_NONE,
        );
        $customerGroups = Mage::helper('tagon_groupscatalog')->getGroups();
        $customerGroupIds = array_merge($customerGroupIds, array_keys($customerGroups->getItems()));

        if (!$data) {
            $data = array(Tagon_GroupsCatalog_Helper_Data::USE_DEFAULT);
        }

        if (!is_array($data)) {
            $data = explode(',', $data);
        }

        if (1 < count($data)) {
            // remove USE_DEFAULT if any other groups are selected, too
            $key = array_search(Tagon_GroupsCatalog_Helper_Data::USE_DEFAULT, $data);
            if (false !== $key) {
                unset($data[$key]);
            }

            // if USE_NONE is selected remove all other groups
            if (in_array(Tagon_GroupsCatalog_Helper_Data::USE_NONE, $data)) {
                $data = array(Tagon_GroupsCatalog_Helper_Data::USE_NONE);
            }
        }

        // validate all customer groups ids are valid
        foreach ($data as $key => $groupId) {
            if (!in_array($groupId, $customerGroupIds)) {
                unset($data[$key]);
            }
        }

        // I like it nice and tidy, this gives us sequential index numbers again as a side effect :)
        sort($data);

        $object->setData($attributeCode, implode(',', $data));
        return parent::beforeSave($object);
    }

    /**
     * Explode the saved array again, because otherwise the indexer will think the value changed,
     * even if it is the same (array(1,2,3) !== "1,2,3").
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterSave($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);
        if (is_string($data)) {
            $object->setData($attributeCode, explode(',', $data));
        }
        return parent::afterSave($object);
    }

    /**
     * In case the data was loaded, explode it into an array
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);

        // only explode and set the value if the attribute is set on the model
        if (null !== $data && is_string($data)) {
            $data = explode(',', $data);
            $object->setData($attributeCode, $data);
        }
        return parent::afterLoad($object);
    }
}
