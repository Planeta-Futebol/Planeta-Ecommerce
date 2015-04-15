<?php
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('stock_product')};
CREATE TABLE {$this->getTable('stock_product')} (
  `index_stock_id` int(11) unsigned NOT NULL auto_increment,
  `stockproductid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `wstoreids` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  `stockprodsku` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`index_stock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('stock_Saleperpartner')};
CREATE TABLE {$this->getTable('stock_Saleperpartner')} (
  `index_stock_saleid` int(11) unsigned NOT NULL auto_increment,
  `stockprodid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `sale_at` datetime NOT NULL,
  `qty_sold` varchar(255) NOT NULL,
  `price_bought` decimal(12,4) NOT NULL default '0',
  `price_sold` decimal(12,4) NOT NULL default '0',
  PRIMARY KEY (`index_stock_saleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
");

$installer->endSetup();
?>
