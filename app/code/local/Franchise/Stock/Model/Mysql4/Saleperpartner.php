<?php
class Franchise_Stock_Model_Mysql4_Saleperpartner extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('stock/Saleperpartner', 'index_stock_saleid');
    }
}
?>
