<?php
$installer = $this;

$installer->startSetup();

$installer->setConfigData('ajaxcart/configuration/enabled_jump', 			1);
$installer->setConfigData('ajaxcart/popup_configuration/product_number',  	'2');
$installer->setConfigData('ajaxcart/dragdrop/floater_text_color',  			'#FFFFFF');
$installer->setConfigData('ajaxcart/dragdrop/floater_bkg',  				'#FF3000');

$installer->endSetup(); 