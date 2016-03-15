<?php
$installer = $this;

$installer->startSetup();

$installer->setConfigData('ajaxcartx/configuration/enabled_jump', 			1);
$installer->setConfigData('ajaxcartx/popup_configuration/product_number',  	'2');
$installer->setConfigData('ajaxcartx/dragdrop/floater_text_color',  			'#FFFFFF');
$installer->setConfigData('ajaxcartx/dragdrop/floater_bkg',  				'#FF3000');

$installer->endSetup(); 