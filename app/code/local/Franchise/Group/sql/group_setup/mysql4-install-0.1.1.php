<?php

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$setup->addAttribute('customer', 'referenceone', array(

        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Referência 1',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
));



$setup->addAttribute('customer', 'referencetwo', array(

        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Referência 2',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
));



$setup->addAttribute('customer', 'phoneone', array(

        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Telefone 1',
        'global' => 1,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'default' => '0',
        'visible_on_front' => 1,
));



$setup->addAttribute('customer', 'phonetwo', array(

        'type' => 'varchar',
        'input' => 'text',
        'label' => 'Telefone 2',
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
            ->getAttribute('customer', 'referenceone')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();



    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'referencetwo')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();

    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'phoneone')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();



    Mage::getSingleton('eav/config')
            ->getAttribute('customer', 'phonetwo')
            ->setData('used_in_forms', array('adminhtml_customer', 'customer_account_create'))
            ->save();

}

$installer->endSetup();