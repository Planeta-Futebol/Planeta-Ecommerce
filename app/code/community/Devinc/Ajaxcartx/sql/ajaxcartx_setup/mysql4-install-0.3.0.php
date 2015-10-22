<?php
$installer = $this;

$installer->startSetup();

$installer->setConfigData('ajaxcartx/configuration/enabled', 										0);
$installer->setConfigData('ajaxcartx/configuration/enabled_jump', 									0);

$installer->setConfigData('ajaxcartx/loader_configuration/petal1',  									'#54ABB4');
$installer->setConfigData('ajaxcartx/loader_configuration/petal2',  									'#96E5EE');
$installer->setConfigData('ajaxcartx/loader_configuration/petal3',  									'#DEDEDE');

$installer->setConfigData('ajaxcartx/popup_configuration/notification_popup',  						1);
$installer->setConfigData('ajaxcartx/popup_configuration/autohide_notification_popup',  				'7');
$installer->setConfigData('ajaxcartx/popup_configuration/product_number',  							'2');
$installer->setConfigData('ajaxcartx/popup_configuration/notification_popup_bkg',  					'#FFFFFF');
$installer->setConfigData('ajaxcartx/popup_configuration/enable_notification_popup_wrapper_bkg', 	0);
$installer->setConfigData('ajaxcartx/popup_configuration/notification_popup_wrapper_bkg', 			'#FFFFFF');
$installer->setConfigData('ajaxcartx/popup_configuration/notification_popup_bodersize',  			1);
$installer->setConfigData('ajaxcartx/popup_configuration/notification_popup_bodercolor',  			'#E6E6E6');
$installer->setConfigData('ajaxcartx/popup_configuration/options_popup_width',  						'500');
$installer->setConfigData('ajaxcartx/popup_configuration/success_popup_width',  						'400');

$installer->setConfigData('ajaxcartx/qty_configuration/qty_button_text_color',  						'#FFFFFF');
$installer->setConfigData('ajaxcartx/qty_configuration/qty_button_bkg_color',  						'#F16022');
$installer->setConfigData('ajaxcartx/qty_configuration/show_qty_in_categorypage',  					1);
$installer->setConfigData('ajaxcartx/qty_configuration/qty_buttons_in_categorypage', 				1);
$installer->setConfigData('ajaxcartx/qty_configuration/qty_buttons_in_productpage',  				1);
$installer->setConfigData('ajaxcartx/qty_configuration/show_qty_in_cartsidebar',  					1);
$installer->setConfigData('ajaxcartx/qty_configuration/qty_buttons_in_cartsidebar',  				1);
$installer->setConfigData('ajaxcartx/qty_configuration/qty_buttons_in_popup',  						1);
$installer->setConfigData('ajaxcartx/qty_configuration/qty_buttons_in_cartpage',  					1);
$installer->setConfigData('ajaxcartx/qty_configuration/qty_buttons_in_wishlist',  					1);

$installer->setConfigData('ajaxcartx/dragdrop/enable_category_dragdrop',  							0);
$installer->setConfigData('ajaxcartx/dragdrop/dragme_text',  										'Drag me');
$installer->setConfigData('ajaxcartx/dragdrop/dragme_text_color',  									'#FFFFFF');
$installer->setConfigData('ajaxcartx/dragdrop/dragme_text_bkg',  									'#FF3000');
$installer->setConfigData('ajaxcartx/dragdrop/drop_effect',  										'shrink');
$installer->setConfigData('ajaxcartx/dragdrop/droppable_highlight_area_color',  						'#FC5831');
$installer->setConfigData('ajaxcartx/dragdrop/tooltip_enable',  										1);
$installer->setConfigData('ajaxcartx/dragdrop/tooltip_cart_text',  									'BUY');
$installer->setConfigData('ajaxcartx/dragdrop/tooltip_compare_text',  								'COMPARE');
$installer->setConfigData('ajaxcartx/dragdrop/tooltip_wishlist_text',  								'WISH');
$installer->setConfigData('ajaxcartx/dragdrop/tooltip_text',  										'#FFFFFF');
$installer->setConfigData('ajaxcartx/dragdrop/tooltip_bkg',  										'#FC4A26');
$installer->setConfigData('ajaxcartx/dragdrop/floater_text_color',  									'#FFFFFF');
$installer->setConfigData('ajaxcartx/dragdrop/floater_bkg',  										'#FF3000');

//ultimo theme specific configuration
$installer->setConfigData('ajaxcartx/ultimo_configuration/cart_sidebar_left',  						0);
$installer->setConfigData('ajaxcartx/ultimo_configuration/cart_sidebar_right',  						0);
$installer->setConfigData('ajaxcartx/ultimo_configuration/compare_sidebar_left',  					0);
$installer->setConfigData('ajaxcartx/ultimo_configuration/compare_sidebar_right',  					0);

$installer->setConfigData('devinc/install/relogin',  												1);

$installer->endSetup(); 