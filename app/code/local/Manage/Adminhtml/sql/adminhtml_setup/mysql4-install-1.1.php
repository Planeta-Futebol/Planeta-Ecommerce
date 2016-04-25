<?php
/**
 * This file adds new fields to the User base, allowing also be viewed and changed in the admin.
 *
 * @author Ronildo dos Santos
 * @date 24/08/2015
 */

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$setup->addAttribute('customer', 'credit_limit', array(

        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Limite de Crédito',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
));

$setup->addAttribute('customer', 'open_value', array(

        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Valor em Aberto',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
));

$setup->addAttribute('customer', 'has_stande', array(

        'type' => 'int',
        'source' => 'eav/entity_attribute_source_boolean',
        'input' => 'select',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,

        'label' => 'Tem Stande',
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
));

$setup->addAttribute('customer', 'payment_range', array(

        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Intervalo de Pagamentos',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{

    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'credit_limit')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();

    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'open_value')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();

    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'has_stande')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();

    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'payment_range')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();
}

$installer->endSetup();