<?php

$installer = $this;

$installer->startSetup();

$tablequote = $this->getTable('sales/quote');

/*==============================================================================
 *  Adiciona o campo Escolaridade no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'escolaridade', array(
	'type' => 'varchar', //int
	'input' => 'text',  //select
	'label' => 'Escolaridade',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '',
	'visible_on_front' => 1
    //,'source' =>	 'onepagecheckout/entity_escolaridade'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
	->getAttribute('customer', 'escolaridade')
	->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
	->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `escolaridade` VARCHAR(100) NULL");

/*==============================================================================
 *  Adiciona o campo Formação no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'formacao', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'Formação',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'default' => '',
  'visible_on_front' => 1
    //,'source' =>   'onepagecheckout/entity_formacao'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
  Mage::getSingleton('eav/config')
  ->getAttribute('customer', 'formacao')
  ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
  ->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `formacao` VARCHAR(100) NULL");

/*==============================================================================
 *  Adiciona o campo Conhecimento do Negocio no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'conhecimento_negocio', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'Conhecimento do Negocio',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'default' => '',
  'visible_on_front' => 1
    //,'source' =>   'onepagecheckout/entity_conhecimento_negocio'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
  Mage::getSingleton('eav/config')
  ->getAttribute('customer', 'conhecimento_negocio')
  ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
  ->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `conhecimento_negocio` VARCHAR(50) NULL");

/*==============================================================================
 *  Adiciona o campo O que mais te atraiu para a franquia no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'atracao_franquia', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'O que mais te atraiu para a franquia',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'default' => '',
  'visible_on_front' => 1
    //,'source' =>   'onepagecheckout/entity_atracao_franquia'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
  Mage::getSingleton('eav/config')
  ->getAttribute('customer', 'atracao_franquia')
  ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
  ->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `atracao_franquia` VARCHAR(100) NULL");

/*==============================================================================
 *  Adiciona o campo franquia_principal_fonte_renda no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'franquia_principal_fonte_renda', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'O que mais te atraiu para a franquia',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'default' => '',
  'visible_on_front' => 1
    //,'source' =>   'onepagecheckout/entity_franquia_principal_fonte_renda'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
  Mage::getSingleton('eav/config')
  ->getAttribute('customer', 'franquia_principal_fonte_renda')
  ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
  ->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `franquia_principal_fonte_renda` VARCHAR(30) NULL");


/*==============================================================================
 *  Adiciona o campo tera_socio_franquia no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'tera_socio_franquia', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'Você terá um sócio',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'default' => '',
  'visible_on_front' => 1
    //,'source' =>   'onepagecheckout/entity_tera_socio_franquia'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
  Mage::getSingleton('eav/config')
  ->getAttribute('customer', 'tera_socio_franquia')
  ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
  ->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `tera_socio_franquia` VARCHAR(30) NULL");


/*==============================================================================
 *  Adiciona o campo estado_cidade_franquia no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'estado_cidade_franquia', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'Estado ou cidade que pretende montar a franquia',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'default' => '',
  'visible_on_front' => 1
    //,'source' =>   'onepagecheckout/entity_estado_cidade_franquia'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
  Mage::getSingleton('eav/config')
  ->getAttribute('customer', 'estado_cidade_franquia')
  ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
  ->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `estado_cidade_franquia` VARCHAR(200) NULL");


/*==============================================================================
 *  Adiciona o campo qual_time_torce no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'qual_time_torce', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'Qual Time Você torce',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'default' => '',
  'visible_on_front' => 1
    //,'source' =>   'onepagecheckout/entity_qual_time_torce'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
  Mage::getSingleton('eav/config')
  ->getAttribute('customer', 'qual_time_torce')
  ->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register'))
  ->save();
};

$installer->run("ALTER TABLE  $tablequote ADD `qual_time_torce` VARCHAR(50) NULL");

$installer->endSetup();
