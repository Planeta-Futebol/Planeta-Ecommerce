<?php
class Wcl_ReportNewOrders_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $filters = array(
        'report_type_products'  => 0,
        'report_group_products' => null,
        'report_district' => null
    );

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters( array $filters )
    {
        $this->filters = $filters;
    }


    public function getAttributesUsed( $attributeCode )
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');
        $idsAttribute = array();

        $eav_attribute_options = $resource->getTableName('eav/attribute_option_value');

        $attributeModel = Mage::getSingleton('eav/config')
                ->getAttribute('catalog_product', $attributeCode);

        $select = $readConnection
                ->select()
                ->from($attributeModel->getBackend()->getTable(), array('value'))
                ->where('attribute_id=?', $attributeModel->getId())
                ->distinct();

        $usedAttributeValues = $readConnection
                ->fetchCol($select);

        foreach ($usedAttributeValues as $id) {
            if($id){ array_push($idsAttribute, intval($id)); }
        }

        $select2 = $readConnection
                ->select()
                ->from($eav_attribute_options, array('name' => 'value', 'option_id'))
                ->where('option_id IN (?)', $idsAttribute);


        $usedAttributeArray = Mage::getSingleton('core/resource')
                ->getConnection('default_read')
                ->fetchAll($select2);

        return $usedAttributeArray;
    }

}