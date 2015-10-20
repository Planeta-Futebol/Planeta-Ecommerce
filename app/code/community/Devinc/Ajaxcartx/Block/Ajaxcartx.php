<?php
class Devinc_Ajaxcartx_Block_Ajaxcartx extends Mage_Checkout_Block_Onepage_Abstract
{    	
    //returns the ajaxcartx initialize url with all the add to cart parameters
    public function getInitUrl()
    {
		return Mage::helper('ajaxcartx')->getInitUrl();
    }   
	
	//returns the min/max qty's for products from the product page, options popup and cart sidebar items
	public function getAjaxCartQtysJs()
	{
		return Mage::getModel('ajaxcartx/ajaxcartx')->getAjaxCartQtysJs();	
	}
	
	public function getLocation() {
		if (Mage::app()->getRequest()->getControllerName() == 'product_compare' && Mage::app()->getRequest()->getActionName() == 'index') {
			return 'setPLocation';
		} else {
			return 'setLocation';
		}
	}
		
	public function getAjaxCartConfig()
	{			
		//LOAD JS CONFIG		
		//Preload loader image
		$html = '<script type="text/javascript">
					//<![CDATA[
					//Preload loader image
					var images = new Array();
					images[0] = new Image();
					images[0].src = "'.$this->getSkinUrl('ajaxcartx/images/opc-ajax-loader.gif').'";';	
		
		//load global variables	
		// $html .= 'var dragDropProducts = new Array();';
				
		//set ajaxcartx login config	
		$isLogged = (Mage::helper('customer')->isLoggedIn()) ? 'true' : 'false';
		$html .= 'ajaxcartxLogin.isLoggedIn = '.$isLogged.';
				  ajaxcartxLogin.defaultWelcomeMsg = \''.str_replace("'","\'",Mage::getStoreConfig('design/header/welcome')).'\';';
				  
		//set ajaxcartx config	
		$rgbNotificationBkgColor = (Mage::getStoreConfig('ajaxcartx/popup_configuration/notification_popup_wrapper_bkg')) ? Mage::helper('ajaxcartx')->hex2rgb(Mage::getStoreConfig('ajaxcartx/popup_configuration/notification_popup_wrapper_bkg')) : '0, 0, 0';
		$rgbBoxShadowColor = (Mage::getStoreConfig('ajaxcartx/dragdrop/dragme_text_bkg')) ? Mage::helper('ajaxcartx')->hex2rgb(Mage::getStoreConfig('ajaxcartx/dragdrop/dragme_text_bkg')) : '0, 0, 0';
		$rgbFloaterBkgColor = (Mage::getStoreConfig('ajaxcartx/dragdrop/floater_bkg')) ? Mage::helper('ajaxcartx')->hex2rgb(Mage::getStoreConfig('ajaxcartx/dragdrop/floater_bkg')) : '0, 0, 0';
		$rgbFloaterTextColor = (Mage::getStoreConfig('ajaxcartx/dragdrop/floater_text_color')) ? Mage::helper('ajaxcartx')->hex2rgb(Mage::getStoreConfig('ajaxcartx/dragdrop/floater_text_color')) : '255,255,255';
		$rgbProductImageHoverTextColor = (Mage::getStoreConfig('ajaxcartx/dragdrop/dragme_text_color')) ? Mage::helper('ajaxcartx')->hex2rgb(Mage::getStoreConfig('ajaxcartx/dragdrop/dragme_text_color')) : '255,255,255';
		$rgbTooltipBkgColor = (Mage::getStoreConfig('ajaxcartx/dragdrop/tooltip_bkg')) ? Mage::helper('ajaxcartx')->hex2rgb(Mage::getStoreConfig('ajaxcartx/dragdrop/tooltip_bkg')) : '0, 0, 0';

		//mobile/tablet notification
		if (Mage::getStoreConfig('ajaxcartx/dragdrop/enable_category_dragdrop') && Mage::helper('ajaxcartx')->isTablet() && !Mage::getModel('core/cookie')->get('ajaxcartx-mobile-notification')) {
			Mage::getModel('core/cookie')->set('ajaxcartx-mobile-notification', true, 86400);
			$html .= 'Event.observe(window, \'load\', function() { setTimeout( function() { alert("'.$this->__('Hold your finger over a Product Image to drag it to the cart, it\'s FUN!').'"); },300) });';
		}

		$html .= 'var ajaxcartx = new Ajaxcartx({
					urls: {
						initialize: \''.$this->getInitUrl().'\',
						updateCart: \''.$this->helper('ajaxcartx')->getUpdateCartUrl().'\',
						clearCart: \''.$this->helper('ajaxcartx')->getClearCartUrl().'\',
						addWishlistItemToCart: \''.$this->helper('wishlist')->getAddToCartUrl('%item%').'\',
						addAllItemsToCart: \''.$this->getUrl('ajaxcartx/wishlist/allcart', array('wishlist_id' => Mage::helper('wishlist')->getWishlist()->getId())).'\'
					},
					configuration: {
						jump: \''.Mage::getStoreConfig('ajaxcartx/configuration/enabled_jump').'\',
						show_notification: \''.Mage::getStoreConfig('ajaxcartx/popup_configuration/notification_popup').'\',
						autohide_notification_time: \''.Mage::getStoreConfig('ajaxcartx/popup_configuration/autohide_notification_popup').'\',
						show_checkout_button: \''.Mage::getStoreConfig('ajaxcartx/popup_configuration/show_checkout_button').'\',
						notification_bkg: \''.Mage::getStoreConfig('ajaxcartx/popup_configuration/enable_notification_popup_wrapper_bkg').'\',
						notification_wrapper_bkg: \''.$rgbNotificationBkgColor.'\',
						box_shadow_color: \''.$rgbBoxShadowColor.'\',
						is_mobile: '.var_export(Mage::helper('ajaxcartx')->isMobile(), true).',
						is_tablet: '.var_export(Mage::helper('ajaxcartx')->isTablet(), true).',
						is_secure: '.var_export(Mage::app()->getStore()->isCurrentlySecure(), true).'
					},
					qtys: {
						category_qty: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/show_qty_in_categorypage').'\',
						category_qty_buttons: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_buttons_in_categorypage').'\',				
						product_qty_buttons: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_buttons_in_productpage').'\',
						popup_qty_buttons: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_buttons_in_popup').'\',
						cartpage_qty_buttons: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_buttons_in_cartpage').'\',
						sidebar_qty: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/show_qty_in_cartsidebar').'\',
						sidebar_qty_buttons: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_buttons_in_cartsidebar').'\',
						wishlist_qty_buttons: \''.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_buttons_in_wishlist').'\'
					},
					dragdrop: {
						dragdrop_enable_category : \''.Mage::getStoreConfig('ajaxcartx/dragdrop/enable_category_dragdrop').'\',
						dragdrop_drop_effect : \''.Mage::getStoreConfig('ajaxcartx/dragdrop/drop_effect').'\',
						tooltip_enable : \''.Mage::getStoreConfig('ajaxcartx/dragdrop/tooltip_enable').'\',
						product_image_icon_color : \''.Mage::getStoreConfig('ajaxcartx/dragdrop/dragme_text_color').'\',
						floater_icon_color : \''.Mage::getStoreConfig('ajaxcartx/dragdrop/floater_text_color').'\'
					}
				});
				
				ajaxcartx.ajaxCartLoadingHtml = \'<span id="ajax-cart-please-wait" class="please-wait" ><img src="'.$this->getSkinUrl('ajaxcartx/images/opc-ajax-loader.gif').'" alt="'.$this->__('Please wait...').'" title="'.$this->__('Please wait...').'" class="v-middle" /><span>'.$this->__('Please wait...').'</span></span>\';
				
				Translator.add(\'Are you sure you would like to remove this item from the shopping cart?\',\''.str_replace("'", "\'", $this->__('Are you sure you would like to remove this item from the shopping cart?')).'\');
				Translator.add(\'The requested quantity is not available.\',\''.str_replace("'", "\'", $this->__('The requested quantity is not available.')).'\');
				Translator.add(\'The minimum quantity allowed for purchase is \',\''.str_replace("'", "\'", $this->__('The minimum quantity allowed for purchase is ')).'\');
				Translator.add(\'The maximum quantity allowed for purchase is \',\''.str_replace("'", "\'", $this->__('The maximum quantity allowed for purchase is ')).'\');
				Translator.add(\'Are you sure you would like to remove this item from the shopping cart?\',\''.str_replace("'", "\'", $this->__('Are you sure you would like to remove this item from the shopping cart?')).'\');
				Translator.add(\'Are you sure you would like to remove this item from the wishlist?\',\''.str_replace("'", "\'", $this->__('Are you sure you would like to remove this item from the wishlist?')).'\');
				Translator.add(\'Update Cart\',\''.str_replace("'", "\'", $this->__('Update Cart')).'\');
				Translator.add(\'Empty Cart\',\''.str_replace("'", "\'", $this->__('Empty Cart')).'\');
				Translator.add(\'Are you sure you would like to remove all the items from the shopping cart?\',\''.str_replace("'", "\'", $this->__('Are you sure you would like to remove all the items from the shopping cart?')).'\');
				Translator.add(\'Update Wishlist\',\''.str_replace("'", "\'", $this->__('Update Wishlist')).'\');				
				
				Translator.add(\'Drag me\',\''.str_replace("'", "\'", Mage::getStoreConfig('ajaxcartx/dragdrop/dragme_text')).'\');
				Translator.add(\'Buy Me!\',\''.str_replace("'", "\'", Mage::getStoreConfig('ajaxcartx/dragdrop/tooltip_cart_text')).'\');
				Translator.add(\'Wish Me!\',\''.str_replace("'", "\'", Mage::getStoreConfig('ajaxcartx/dragdrop/tooltip_wishlist_text')).'\');
				Translator.add(\'Compare Me!\',\''.str_replace("'", "\'", Mage::getStoreConfig('ajaxcartx/dragdrop/tooltip_compare_text')).'\');
			//]]>
			</script>';
			
		//LOAD THEME SPECIFIC CONFIG
		//set default values
		$html .= '<script type="text/javascript">
					var disablePopupProductLoader = false;
					var cartLink = false;
					var wishlistLink = false;
					var droppableSidebars = new Array();
					droppableSidebars["cart"] = true;
					droppableSidebars["wishlist"] = true;
					droppableSidebars["compare"] = true;
					function dispatchBlockUpdates(response){}
					function dispatchButtonUpdates(button, onClick){}
					function dispatchLinkUpdates(button, onClick){}
					function dispatchLiveUpdates(type, element){}
					function dispatchJump(imageContainerClone){}
				  </script>';	
				  				 
		if (Mage::getSingleton('core/design_package')->getTheme('default')=='groupdeals') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/gdtheme.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/gdtheme.css').'" />';
		} else if (Mage::getSingleton('core/design_package')->getPackageName()=='enterprise') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/enterprise.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/enterprise.css').'" />';
		} else if (Mage::getSingleton('core/design_package')->getPackageName()=='ultimo') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/ultimo.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/ultimo.css').'" />';
		} else if (Mage::getSingleton('core/design_package')->getPackageName()=='webmarket') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/webmarket.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/webmarket.css').'" />';
		} else if (Mage::getSingleton('core/design_package')->getPackageName()=='rwd') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/rwd.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/rwd.css').'" />';
		} else if (Mage::getSingleton('core/design_package')->getPackageName()=='shopper') {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/shopper.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/shopper.css').'" />';
		} else if (Mage::getSingleton('core/design_package')->getPackageName()=='fortis') {
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/fortis.css').'" />';
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/fortis.js').'"></script>';
		}

		if (file_exists(Mage::getBaseDir().DS.'skin'.DS.'frontend'.DS.'base'.DS.'default'.DS.'ajaxcartx'.DS.'themes'.DS.'custom.js')) {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/themes/custom.js').'"></script>';
		} 
		if (file_exists(Mage::getBaseDir().DS.'skin'.DS.'frontend'.DS.'base'.DS.'default'.DS.'ajaxcartx'.DS.'themes'.DS.'custom.css')) {
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/themes/custom.css').'" />';
		}
		
		//LOAD EXTENSION SPECIFIC CONFIG
	    if (Mage::helper('core')->isModuleEnabled('Devinc_Groupdeals') && Mage::helper('groupdeals')->isEnabled()) {
			$html .= '<script type="text/javascript" src="'.$this->getSkinUrl('ajaxcartx/extensions/groupdeals.js').'"></script>';
			$html .= '<link rel="stylesheet" type="text/css" href="'.$this->getSkinUrl('ajaxcartx/extensions/groupdeals.css').'" />';			
		}	
		
		//UPDATE AJAXCART BLOCKS
		$html .= '<script type="text/javascript">ajaxcartx.updateAjaxCartBlocks(false);</script>';	

		//LOAD CSS CONFIG
		$html .= '<style>
					.popup-container .popup-content { 
						background:'.Mage::getStoreConfig('ajaxcartx/popup_configuration/notification_popup_bkg').' !important;
						border:'.Mage::getStoreConfig('ajaxcartx/popup_configuration/notification_popup_bodersize').'px solid '.Mage::getStoreConfig('ajaxcartx/popup_configuration/notification_popup_bodercolor').' !important;
					}
					#ajaxcartx-login-popup-content { width:50%; max-width:310px; } 				
					#ajaxcartx-loading-popup-container.popup-container .popup-content { background:none !important; border:0 !important; }
					
					.qty-control-box button.increase { background:'.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_button_bkg_color').' !important; }
					.qty-control-box button.decrease { background:'.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_button_bkg_color').' !important; }	
					.qty-control-box button.increase span{ color:'.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_button_text_color').' !important; background:'.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_button_increase_bkg_color').' !important; }
					.qty-control-box button.decrease span{ color:'.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_button_text_color').' !important; background:'.Mage::getStoreConfig('ajaxcartx/qty_configuration/qty_button_decrease_bkg_color').' !important; }
					
					.draggable-over { border:1px solid '.Mage::getStoreConfig('ajaxcartx/dragdrop/droppable_highlight_area_color').' !important; box-shadow: 0 0 4px '.Mage::getStoreConfig('ajaxcartx/dragdrop/droppable_highlight_area_color').'; }
					.draggable-bkg .draggable-text { color:'.Mage::getStoreConfig('ajaxcartx/dragdrop/dragme_text_color').' !important; }
					.ac-floater { background: rgba('.$rgbFloaterBkgColor.', 0.8); }
					.ac-floater .draggable-content .draggable-text { color:'.Mage::getStoreConfig('ajaxcartx/dragdrop/floater_text_color').' !important; }

					.tooltip-sidebar { background:rgba('.$rgbTooltipBkgColor.', 1) !important; color:'.Mage::getStoreConfig('ajaxcartx/dragdrop/tooltip_text').' !important; }
					.tooltip-sidebar:after { border-color:rgba('.$rgbTooltipBkgColor.', 1) transparent transparent !important; }

					#petal-1 { background: '.Mage::getStoreConfig('ajaxcartx/loader_configuration/petal1').' !important; }
					#petal-2 { background: '.Mage::getStoreConfig('ajaxcartx/loader_configuration/petal2').' !important; }
					#petal-3 { background: '.Mage::getStoreConfig('ajaxcartx/loader_configuration/petal3').' !important; }';	
		$html .= '</style>';
			
		return $html;
	}	
}