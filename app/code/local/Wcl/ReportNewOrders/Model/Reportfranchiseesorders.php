<?php

class Wcl_ReportNewOrders_Model_Reportfranchiseesorders extends Wcl_ReportNewOrders_Model_Reportneworders
{
    public function addOrderedQty( $from = '', $to = '' )
    {
        $collection = Mage::getModel("stock/Saleperpartner")->getCollection();

        $collection->getSelect()->joinLeft(
                array('cust' => $collection->getTable('catalog/product')),
                'cust.entity_id = main_table.stockprodid', array('*'));

        $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');

        if(!empty($name) && $name!="Por nome do produto") {
            $collection->getSelect()
                    ->join(array($alias => $attribute->getBackendTable()),
                            "main_table.stockprodid = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
                            array('name' => 'value'))->where("name_table.value LIKE ?", "%$name%");
        } else {
            $collection->getSelect()
                    ->join(array($alias => $attribute->getBackendTable()),
                            "main_table.stockprodid = $alias.entity_id AND $alias.attribute_id={$attribute->getId()}",
                            array('name' => 'value'));
        }

        echo $this->getSelect();
        exit;

        return $this;
    }
}