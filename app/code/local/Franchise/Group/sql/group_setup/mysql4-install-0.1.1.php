<?php

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'franchiseoption', array(
        'type' => 'int',
        'input' => 'select',
        'label' => 'Opção Franquia',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
        'source' => 'group/entity_franchiseoption',
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'franchiseoption')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();

}

$installer->endSetup();