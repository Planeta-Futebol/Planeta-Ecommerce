<?php
class Franchise_Stock_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('stock/product', 'index_stock_id');
    }
}
?>
