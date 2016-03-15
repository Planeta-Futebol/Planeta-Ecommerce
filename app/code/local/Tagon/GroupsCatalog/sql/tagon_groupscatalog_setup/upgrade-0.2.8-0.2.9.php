<?php


/* @var $installer Tagon_GroupsCatalog_Model_Resource_Setup */
$installer = $this;


$installer->startSetup();

foreach (array('catalog_product', 'catalog_category') as $type) {
    $installer->dropIndexTable($type);
    $installer->createIndexTable($type);
}

$installer->endSetup();
