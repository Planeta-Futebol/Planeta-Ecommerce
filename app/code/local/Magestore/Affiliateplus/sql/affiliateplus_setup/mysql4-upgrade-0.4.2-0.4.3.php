<?php
/*
* Added by Alex 27/04/2015
*/

$installer = $this;
$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('affiliateplus/account')}
  ADD COLUMN `invited_id` int(10) unsigned NOT NULL,
  ADD FOREIGN KEY (`invited_id`) REFERENCES {$this->getTable('affiliateplus/account')} (`account_id`) ON UPDATE CASCADE;

");

$installer->getConnection()->resetDdlCache($this->getTable('affiliateplus/account'));

$installer->endSetup();
