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
  PRIMARY KEY (`index_stock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
");

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('stock_product_sale')};
CREATE TABLE {$this->getTable('stock_product_sale')} (
  `index_stock_sale_id` int(11) unsigned NOT NULL auto_increment,
  `stockproductid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `wstoreids` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  `qty_sale` varchar(255) NOT NULL,
  `price_sale` decimal(12,4) NOT NULL default '0',
  PRIMARY KEY (`index_stock_sale_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
");

$installer->endSetup();
?>
