<?php
class Devinc_Ajaxcart_IndexController extends Mage_Checkout_Controller_Action 
{	   	
	protected function _initProduct()    
	{
		$productId = $this->getRequest()->getParam('product',false); 
		if ($productId) {            
			$product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);            
			if ($product->getId()) {
				Mage::unregister('current_product');
				Mage::register('current_product',$product);				
				Mage::unregister('product');
				Mage::register('product',$product);
				return $product;           
			}        
		}       
		return false;    
	}	
	
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }

	//initialize ajaxcart; add the product to the cart or open the product options popup
	public function initAction()	
	{
        $result = array();
        $params = $this->getRequest()->getParams();	
        if (isset($params['redirect_url'])) {
        	$redirectUrl = $params['redirect_url'];
        }

		if (!Mage::helper('ajaxcart')->isEnabled()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The Ajax Cart extension is disabled.'));
			$result['redirect'] = $redirectUrl;
			
			Mage::getModel('license/module')->runAjax($result, $this);
        	return;
        }

		if (!is_array(Mage::getModel('license/module')->getAcBlocks()) && isset($params['redirect_url'])) {
            Mage::getSingleton('checkout/session')->addError($this->__('Your session has expired.'));
			$result['redirect'] = $redirectUrl;
			
			Mage::getModel('license/module')->runAjax($result, $this);
        	return;
        }
		
		$product = $this->_initProduct();
		if (!$product) {
			$result['redirect'] = $redirectUrl;
			
			Mage::getModel('license/module')->runAjax($result, $this);
			return;
		}

		if (!isset($params['skip_popup']) && ($this->_hasOptions($product) || $product->isGrouped())) {
			if (isset($params['close_popup'])) {
				$result['close_popup'] = $params['close_popup'];
			}
			$result['popup'] = 'options';
			$result['form_action'] = Mage::helper('ajaxcart')->getInitUrl();
			if (isset($params['qty'])) {
				$result['qty'] = $params['qty'];
			}
			$result['product_id'] = $product->getId();
				
			if (!$product->isGrouped()){
				if ($product->getTypeId()=='configurable'){
					Mage::getSingleton('customer/session')->setAjaxCartAction('options_popup_conf');
					$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getConfigurableHtml($this);
					Mage::getSingleton('customer/session')->setAjaxCartAction();
				} else if ($product->getTypeId()=='bundle'){
					$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getBundleHtml($this);
				} else if ($product->getTypeId()=='downloadable'){
					$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getDownloadableHtml($this);
				} else if ($product->getTypeId()=='giftcard'){
					$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getGiftCardHtml($this);
				}  else {
					$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getOptionsHtml($this);				
				}
			} else {
				$result['update_section']['html_options'] = Mage::helper('ajaxcart')->getGroupedHtml($this);
			}
		} else {
			//add product to cart	
			$cart = Mage::getSingleton('checkout/cart');
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}
			
				$related = $this->getRequest()->getParam('related_product');
			
			
				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}
			
				$cart->save();
				
				Mage::dispatchEvent('checkout_cart_add_product_complete',
					array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);  
				
                $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                Mage::getSingleton('checkout/session')->addSuccess($message);
			} catch (Mage_Core_Exception $e) {
				$result['error'] = -1;
				if (Mage::getSingleton('catalog/session')->getUseNotice(true)) {
					$result = array('message' => $e->getMessage());
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));                
					foreach ($messages as $message) {                	
						$result['message'][] = html_entity_decode($message);
					}
				}
				
				Mage::getModel('license/module')->runAjax($result, $this);
				return;
			} catch (Exception $e) {
				$result = array('error' => -1, 'message' => $this->__('Cannot add the item to shopping cart.'));
				Mage::logException($e);
				
				Mage::getModel('license/module')->runAjax($result, $this);
				return;
			}

        	Mage::getSingleton('checkout/session')->setCartWasUpdated(true);			
			
			$result['is_action'] = 'cart';
			$result['close_popup'] = 'options';
			if (isset($params['close_popup'])) {
				$result['close_popup'] = $params['close_popup'];
			}
            
            $result['update_section']['html_layout_messages'] = $this->_getLayoutMessagesHtml();
	        $result['update_section']['html_cart'] = Mage::helper('ajaxcart')->getCartHtml($this);
            $result['update_section']['html_cart_link'] = Mage::helper('ajaxcart')->getCartLinkHtml($this);

            if (Mage::getStoreConfig('ajaxcart/popup_configuration/additional_content')!='') {
            	$type = Mage::getStoreConfig('ajaxcart/popup_configuration/additional_content');
            	$result['update_section']['html_additional_content'] = Mage::helper('ajaxcart')->getAdditionalContentHtml($this, $type);
            }

            if (Mage::getStoreConfig('ajaxcart/popup_configuration/show_product_images')) {
            	$result['update_section']['html_product_images'] = Mage::helper('ajaxcart')->getProductImagesHtml($this);
            }

			//load cart page html only if on the shopping cart page; used when adding cross-sells to the cart or when adding products from the compare popup
			if(isset($params['is_cart_page']) && $params['is_cart_page']) {
           		$result['update_section']['html_cart_page'] = Mage::helper('ajaxcart')->getCartPageHtml($this);
           		$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
           		
		   		if ($this->getRequest()->getParam('is_compare_popup', false)) {
					$result['popup'] = 'success';
		   		}
            } else {            
				$result['popup'] = 'success';
           		$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
            }
		}
		
		Mage::getModel('license/module')->runAjax($result, $this);
	}	

    //initialize login popup
    public function loginAction()
    {		
        $redirectUrl = $this->getRequest()->getParam('redirect_url');
        $result = array();
        
        if (!Mage::helper('ajaxcart')->isEnabled()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The Ajax Cart extension is disabled.'));
			$result['redirect'] = $redirectUrl;
			
			Mage::getModel('license/module')->runAjax($result, $this);
        	return;
        }

		if (!is_array(Mage::getModel('license/module')->getAcBlocks())) {
            Mage::getSingleton('checkout/session')->addError($this->__('Your session has expired.'));
			$result['redirect'] = $redirectUrl;
			
			Mage::getModel('license/module')->runAjax($result, $this);
        	return;
        }
        
    	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $result['redirect'] = $redirectUrl;            
  
			Mage::getModel('license/module')->runAjax($result, $this);
            return;
        }        
    
		$result['popup'] = 'ajaxcart-login';
		if ($closePopup = $this->getRequest()->getParam('close_popup', false)) {
			$result['close_popup'] = $closePopup;
		}
		$result['update_section']['html_login'] = Mage::helper('ajaxcart')->getLoginHtml($this);
		
		Mage::getModel('license/module')->runAjax($result, $this);
    } 	
	
    //Login post action
    public function loginPostAction()
    {	
        $redirectUrl = $this->getRequest()->getParam('redirect_url');
        $result = array();
        
        if (!Mage::helper('ajaxcart')->isEnabled()) {
            Mage::getSingleton('checkout/session')->addError($this->__('The Ajax Cart extension is disabled.'));
			$result['redirect'] = $redirectUrl;
			
			Mage::getModel('license/module')->runAjax($result, $this);
        	return;
        }

		if (!is_array(Mage::getModel('license/module')->getAcBlocks())) {
            Mage::getSingleton('checkout/session')->addError($this->__('Your session has expired.'));
			$result['redirect'] = $redirectUrl;
			
			Mage::getModel('license/module')->runAjax($result, $this);
        	return;
        }
        
    	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $result['redirect'] = $redirectUrl;            
            
			Mage::getModel('license/module')->runAjax($result, $this);
            return;
        }           

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
	                $session = Mage::getSingleton('customer/session');
                    $session->login($login['username'], $login['password']);
                    
					$customer = $session->getCustomer();
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                    	$isJustConfirmed = true;
                        $customer->sendNewAccountEmail(
                        	$isJustConfirmed ? 'confirmed' : 'registered',
            				'',
            				Mage::app()->getStore()->getId()
            			);
                    }
		           	
		           	$result['update_section']['welcome'] = $this->__('Welcome, %s!', Mage::helper('core')->htmlEscape($session->getCustomer()->getName()));
		           	$result['update_section']['html_cart'] = Mage::helper('ajaxcart')->getCartHtml($this);
		            $result['update_section']['html_cart_link'] = Mage::helper('ajaxcart')->getCartLinkHtml($this);
		
					//load cart page html only if on the shopping cart page;
					if($this->getRequest()->getParam('is_cart_page', false)) {
		           		$result['update_section']['html_cart_page'] = Mage::helper('ajaxcart')->getCartPageHtml($this);
		            }		
			        
			        $result['update_section']['html_compare'] = Mage::helper('ajaxcart')->getCompareHtml($this);
			        if ($this->getRequest()->getParam('is_compare_popup', false)) {
				        $result['update_section']['html_compare_popup'] = Mage::helper('ajaxcart')->getComparePopupHtml($this);
				    }
				    
		           	$result['update_section']['html_ajaxcart_js'] = Mage::getModel('ajaxcart/ajaxcart')->getAjaxCartQtysJs();
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            $message = Mage::helper('ajaxcart')->__('This account is not confirmed. Please confirm your email address before logging in.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $message = $e->getMessage();
                            break;
                        default:
                            $message = $e->getMessage();
                    }
                                        
                    $result = array('error' => -1, 'message' => html_entity_decode($message));
                    
					Mage::getModel('license/module')->runAjax($result, $this);
                    return;
                } catch (Exception $e) {
                    // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
                }
            } else {
                $message = $this->__('Login and password are required.');                    
                $result = array('error' => -1, 'message' => $message);
                
				Mage::getModel('license/module')->runAjax($result, $this);
                return;
            }
        }
		
		Mage::getModel('license/module')->runAjax($result, $this);
    }	

	//reset ajaxcart blocks
	public function resetAction()	
	{
		Mage::getModel('license/module')->resetAcBlocks();
        Mage::getSingleton('customer/session')->setAjaxRequest();
	}

    protected function _hasOptions($_product)
    {
        if ($_product->getTypeInstance(true)->hasOptions($_product)) {
            return true;
        }
        return false;
    }
	
    protected function _getLayoutMessagesHtml()
    {        
	    $cache = Mage::app()->getCacheInstance();
		$cache->banUse('layout');
		
		/* Mage::getModel('ajaxcart/ajaxcart')->loadQuoteMessages(); */
        $layout = $this->getLayout();   
        if (Mage::helper('ajaxcart')->getMagentoVersion()>1411) {     
			$this->_initLayoutMessages(array('checkout/session', 'catalog/session', 'customer/session'));
		}
        $update = $layout->getUpdate();
        $update->load('ajaxcart_index_messages');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

}