<?php

class Wcl_ReportNewOrders_Model_Reportfranchiseesorders extends Wcl_ReportNewOrders_Model_Reportneworders
{
    public function getReportData( $from = '', $to = '' )
    {

        $attributeAliasTable = 'f';
        $productAliasTable   = 'e';
        $orderTableAliasName = 'main_table';

        $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');


        $betweenCondition = null;

        if ($from != '' && $to != '') {
            $fieldName = $orderTableAliasName . '.sale_at';
            $betweenCondition = $this->_prepareBetweenSql($fieldName, $from, $to);
        }


        $this->getSelect()->reset()
                ->from(
                    array('main_table' => 'stock_Saleperpartner')
                )
                ->columns(array(
                    'qty_ordered' => 'qty_sold',
                    'unic_price' => 'price_sold',
                    'total_sold' => new Zend_Db_Expr("(price_sold * qty_sold)")
                ))
                ->joinLeft(
                array(
                        $productAliasTable => $this->getTable('catalog/product')),
                        $productAliasTable.'.entity_id = main_table.stockprodid',
                         array(
                                 'sku' => 'e.sku',
                         )
                )->joinInner(array($attributeAliasTable => $attribute->getBackendTable()),
                        "main_table.stockprodid = $attributeAliasTable.entity_id AND $attributeAliasTable.attribute_id={$attribute->getId()}",
                        array(
                                'order_items_name' => 'value',
                                )
                )->where($betweenCondition);

        return $this;
    }
}