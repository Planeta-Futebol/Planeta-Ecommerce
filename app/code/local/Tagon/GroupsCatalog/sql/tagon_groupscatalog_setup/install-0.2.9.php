<?php


/* @var $installer Tagon_GroupsCatalog_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

// Just to be sure the latest version of the attributes is installed
$installer->deleteTableRow(
    'eav/attribute', 'attribute_code', Tagon_GroupsCatalog_Helper_Data::HIDE_GROUPS_ATTRIBUTE
);

foreach (array('catalog_product', 'catalog_category') as $entityType) {
    $installer->addGroupsCatalogAttribute($entityType);
    $installer->dropIndexTable($entityType);
    $installer->createIndexTable($entityType);
}

$installer->endSetup();
