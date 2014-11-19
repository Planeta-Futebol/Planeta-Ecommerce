<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->run('
    CREATE TABLE IF NOT EXISTS `'.$installer->getTable('bling_nf').'` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `order_id` int(10) unsigned NOT NULL DEFAULT "0",
      `status` varchar(255) DEFAULT NULL,
      `status_gateway` varchar(20) DEFAULT NULL,
      `status_message` varchar(255) DEFAULT NULL,
      `error_message` varchar(255) DEFAULT NULL,
      `nf_danfe` varchar(255) DEFAULT NULL,
      `nf_key` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `updated_at` timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",
      PRIMARY KEY (`id`),
      KEY `IDX_BLING_SALES_FLAT_ORDER_PAYMENT_PARENT_ID_PAYMENT` (`order_id`),
      CONSTRAINT `FK_BLING_ORDER_ID_SALES_FLAT_ORDER_ENTITY_ID` FOREIGN KEY (`order_id`) REFERENCES `'.$installer->getTable('sales_flat_order').'` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) DEFAULT CHARSET=utf8;
'); // ENGINE=InnoDB 

$installer->endSetup();
