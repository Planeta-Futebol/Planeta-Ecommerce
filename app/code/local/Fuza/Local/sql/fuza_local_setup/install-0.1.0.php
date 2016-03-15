<?php

$installer = $this;

$installer->startSetup();



/*==============================================================================
 *  Adiciona o campo Tipo Pessoa no billing e shipping
 * =============================================================================
 */
$this->addAttribute('customer_address', 'tipopessoa', array(
  'type' => 'varchar', //int
  'input' => 'text',  //select
  'label' => 'Tipo Pessoa',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'visible_on_front' => 1
  //,'source' =>  'onepagecheckout/entity_tipopessoa'
));
Mage::getSingleton('eav/config')
  ->getAttribute('customer_address', 'tipopessoa')
  ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
  ->save();

$tablequoteTipopessoa = $this->getTable('sales/order_address');
$installer->run("ALTER TABLE  $tablequoteTipopessoa ADD `tipopessoa` VARCHAR(25) NULL");

/*==============================================================================
 *  Adiciona o campo Tipo Pessoa no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'tipopessoa', array(
	'type' => 'varchar', //int
	'input' => 'text',  //select
	'label' => 'Tipo Pessoa',
	'global' => 1,
	'visible' => 1,
	'required' => 1,
	'user_defined' => 1,
	'default' => '',
	'visible_on_front' => 1
    //,'source' =>	 'onepagecheckout/entity_tipopessoa'
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
	->getAttribute('customer', 'tipopessoa')
	->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register','adminhtml_customer_address','customer_address_edit','customer_register_address'))
	->save();
};

$tablequoteTipopessoa2 = $this->getTable('sales/quote');
$installer->run("ALTER TABLE  $tablequoteTipopessoa2 ADD `tipopessoa` VARCHAR(30) NULL");




/*==============================================================================
 *  Adiciona o campo rg no billing e shipping
 * =============================================================================
 */
$this->addAttribute('customer_address', 'rg', array(
  'type' => 'varchar',
  'input' => 'text',
  'label' => 'Identidade',
  'global' => 1,
  'visible' => 1,
  'required' => 0,
  'user_defined' => 1,
  'visible_on_front' => 1
));
Mage::getSingleton('eav/config')
	->getAttribute('customer_address', 'rg')
	->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
	->save();

$tablequoteRG = $this->getTable('sales/order_address');
$installer->run("ALTER TABLE  $tablequoteRG ADD `rg` VARCHAR(25) NULL");

/*==============================================================================
*  Adiciona o campo rg no cadastro do usuario
* =============================================================================
*/
$this->addAttribute('customer', 'rg', array(
	'type' => 'varchar',
	'input' => 'text',
	'label' => 'Identidade',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '',
	'visible_on_front' => 1
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
	Mage::getSingleton('eav/config')
	->getAttribute('customer', 'rg')
	->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register','adminhtml_customer_address','customer_address_edit','customer_register_address'))
	->save();
};

$tablequoteRG2 = $this->getTable('sales/quote');
$installer->run("ALTER TABLE  $tablequoteRG2 ADD `customer_rg` VARCHAR(25) NULL");



/*==============================================================================
 *  Adiciona o campo Inscrição Estadual no billing e shipping
 * =============================================================================
 */
 $this->addAttribute('customer_address', 'ie', array(
      'type' => 'varchar',
      'input' => 'text',
      'label' => 'Inscricao Estadual',
      'global' => 1,
      'visible' => 1,
      'required' => 0,
      'user_defined' => 1,
      'visible_on_front' => 1
  ));
  Mage::getSingleton('eav/config')
      ->getAttribute('customer_address', 'ie')
      ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
      ->save();

$tablequoteIE = $this->getTable('sales/order_address');
$installer->run("ALTER TABLE  $tablequoteIE ADD `ie` VARCHAR(25) NULL");

/*==============================================================================
 *  Adiciona o campo Inscrição Estadual no cadastro do usuario
 * =============================================================================
 */
$this->addAttribute('customer', 'ie', array(
	'type' => 'varchar',
	'input' => 'text',
	'label' => 'Inscricao Estadual',
	'global' => 1,
	'visible' => 1,
	'required' => 0,
	'user_defined' => 1,
	'default' => '',
	'visible_on_front' => 1
));

if (version_compare(Mage::getVersion(), '1.4.2', '>='))
{
    Mage::getSingleton('eav/config')
	->getAttribute('customer', 'ie')
	->setData('used_in_forms', array('adminhtml_customer','customer_account_create','customer_account_edit','checkout_register','adminhtml_customer_address','customer_address_edit','customer_register_address'))
	->save();
};

$tablequoteIE2 = $this->getTable('sales/quote');
$installer->run("ALTER TABLE  $tablequoteIE2 ADD `customer_ie` VARCHAR(25) NULL");




$installer->endSetup();
