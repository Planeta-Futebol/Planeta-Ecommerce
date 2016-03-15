<?php
/**
 * ShopInDev
 *
 * @category    ShopInDev
 * @package     ShopInDev_SuperPageSpeed
 * @copyright   Copyright (c) 2014 ShopInDev
 * @license     http://opensource.org/licenses/GPL-3.0 GNU General Public License (GPL)
 */

class ShopInDev_SuperPageSpeed_Model_Cache {

	const FORM_KEY_PLACEHOLDER = '<!--[FORM_KEY]-->';
	const SESSION_ID_PLACEHOLDER = '<!--[SESSION_ID]-->';
	const BLOCK_PLACEHOLDER = '<!--[BLOCK_ID]-->';

	private $blocksName = array();
	private $blocksHtml = array();
	private $placeholders = array();
	private $canDoCache = NULL;
	private $requestId = NULL;
	private $loaded = FALSE;

	/**
	 * Return if can do cache for that URL
	 * Wrapper to avoid many requests
	 * @return boolean
	 */
	public function canDoCache(){

		if( $this->canDoCache === NULL ){
			$this->canDoCache = Mage::helper('superpagespeed')->canDoCache();
		}

		return $this->canDoCache;
	}

	/**
	 * Retrieve the URL request ID
	 * Wrapper to avoid many requests
	 * @return string
	 */
	public function getCacheRequestId(){

		if( $this->requestId === NULL ){
			$this->requestId = Mage::helper('superpagespeed')->getCacheRequestId();
		}

		return $this->requestId;
	}

	/**
	 * Retrive module cache
	 * @return Mage_Core_Model_Cache
	 */
	public function getCache(){
		return Mage::app()->getCacheInstance();
	}

	/**
	 * Register global objects for dynamic blocks
	 * @return void
	 */
	private function registerGlobalObjects(){

		$controllerName = Mage::app()->getRequest()->getControllerName();
		$id = (int) Mage::app()->getRequest()->getParam('id');
		$categoryId = FALSE;

		// Product
		if( $controllerName == 'product' AND $id ){

			$product = Mage::getModel('catalog/product')
						   ->setStoreId( Mage::app()->getStore()->getId() )
						   ->load( $id );

			Mage::register('current_product', $product, true);
			Mage::register('product', $product, true);

			$categoryId = (int) Mage::app()->getRequest()->getParam('category_id');

		// Category
		}elseif( $controllerName == 'category' AND $id ){
			$categoryId = $id;
		}

		if( $categoryId ){

			$category = Mage::getModel('catalog/category')
							->setStoreId( Mage::app()->getStore()->getId() )
							->load( $categoryId );

			Mage::register('category', $category, true);
			Mage::register('current_category', $category, true);

		}

	}

	/**
	 * Process request
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function processRequest($observer){

		if( !$this->canDoCache() ){

			if( Mage::App()->getResponse()->canSendHeaders() ){
				Mage::App()->getResponse()->setHeader('X-Cache', 'DENY');
			}

			return;
		}

		$data = $this->getCache()->load( $this->getCacheRequestId() );

		if( !$data ){

			if( Mage::App()->getResponse()->canSendHeaders() ){
				Mage::App()->getResponse()->setHeader('X-Cache', 'MISS');
			}

			return;
		}

		$this->loaded = TRUE;

		$data = json_decode($data, TRUE);
		$body = $data['body'];
		$dynamicBlocks = $data['dynamicBlocks'];
		$dynamicXml = simplexml_load_string(
			$data['dynamicXml'], 'Mage_Core_Model_Layout_Element');

		$layout = Mage::App()->getLayout();
		$layout->setXml( $dynamicXml );
		$layout->generateBlocks();

		if( $dynamicBlocks ){
			$this->registerGlobalObjects();
		}

		// Init layout messages (work usually done by controller)
		$messagesTypes = Mage::helper('superpagespeed')->getSessionTypes();

		foreach( $messagesTypes as $storageName ){

			$storage = Mage::getSingleton($storageName);

			if( !$storage ){
				continue;
			}

			$block = $layout->getMessagesBlock();
			$block->addMessages($storage->getMessages(true));
			$block->setEscapeMessageFlag($storage->getEscapeMessages(true));
			$block->addStorageType($storageName);

		}

		// Replace block placeholders with dynamic content
		$layout->getOutput();

		if( $this->placeholders ){
			$body = str_replace($this->placeholders, $this->blocksHtml, $body);
		}

		// Replace form key and session id placeholders with dynamic content
		$session = Mage::getSingleton('core/session');
		$formKey = $session->getFormKey();
		$sid = $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId();

		$body = str_replace(self::FORM_KEY_PLACEHOLDER, $formKey, $body);
		$body = str_replace(self::SESSION_ID_PLACEHOLDER, $sid, $body);

		// Output response
		if( Mage::App()->getResponse()->canSendHeaders() AND $body ){

			Mage::App()->getResponse()->setBody($body);
			Mage::App()->getResponse()->setHeader('X-Cache', 'HIT');
			Mage::App()->getResponse()->setHeader('X-Optimized by', 'Super Page Speed - http://shopindev.com/super-page-speed');
			Mage::App()->getResponse()->sendResponse();

			exit;
		}

	}

	/**
	 * Generate dynamic blocks and placeholders
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function generateDynamicBlocks($observer){

		if( !$this->canDoCache() ){
			return;
		}

		$block = $observer->getEvent()->getBlock();
		$blockName = $block->getNameInLayout();

		if( !Mage::helper('superpagespeed')->canCacheBlock($blockName) ){

			if( in_array($blockName, $this->blocksName) ){
				return;
			}

			$placeholder = str_replace('BLOCK_ID', $blockName, self::BLOCK_PLACEHOLDER);
			$html = $observer->getTransport()->getHtml();

			if( Mage::helper('superpagespeed')->canShowDebug() ){

				$debug = '<div style="position:relative; border:1px solid red; padding:5px; min-height: 20px; margin: 5px; 0">';
					$debug .= '<div style="position:absolute; left:0; top:0; padding:2px 5px; background:red; color:white; font:normal 11px Arial; text-align:left !important; z-index:998;">{NAME}</div>';
					$debug .= "{HTML}";
				$debug .= '</div>';

				$debug = str_replace('{NAME}', $blockName, $debug);
				$html = str_replace('{HTML}', $html, $debug);

			}

			$this->blocksName[] = $blockName;
			$this->blocksHtml[] = $html;
			$this->placeholders[] = $placeholder;

			$observer->getTransport()->setHtml($placeholder);

		}

	}

	/**
	 * Capture body and save in cache
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function captureResponse($observer){

		if( !$this->canDoCache() OR $this->loaded ){
			return;
		}

		$body = (string) $observer->getResponse()->getBody();

		if( !$body ){
			return;
		}

		// Create cache
		$cacheId = $this->getCacheRequestId();
		$lifeTime = Mage::helper('superpagespeed')->getCacheLifetime();
		$tags = Mage::helper('superpagespeed')->getCacheTags();

		// Replace form key and session id with placeholders
		$bodyCache = $body;
		$session = Mage::getSingleton('core/session');
		$formKey = $session->getFormKey();
		$sid = $session->getSessionIdQueryParam() . '=' . $session->getEncryptedSessionId();

		if( $formKey ){
			$bodyCache = str_replace($formKey, self::FORM_KEY_PLACEHOLDER, $bodyCache);
		}

		if( $session->getEncryptedSessionId() ){
			$bodyCache = str_replace($sid, self::SESSION_ID_PLACEHOLDER, $bodyCache);
		}

		// Generate dynamic blocks xml
		$layout = Mage::app()->getLayout();
		$xml = simplexml_load_string( $layout->getXmlString(), 'Mage_Core_Model_Layout_Element');
		$dynamicXml = simplexml_load_string('<layout/>', 'Mage_Core_Model_Layout_Element');
		$types = array('block', 'reference', 'action');

		foreach( $this->blocksName as $blockName ){
			foreach( $types as $type ) {
				$xPath = $xml->xpath("//" . $type . "[@name='" . $blockName . "']");
				foreach( $xPath as $child ){
					$dynamicXml->appendChild($child);
				}
			}
		}

		// Force output toHtml
		$xml = (string) $dynamicXml->asXML();
		$xml = str_replace('<block ', '<block output="toHtml" ', $xml);

		// Remove line breaks, tab spaces and double spaces
		if( Mage::helper('superpagespeed')->canMinifyHTML() ){
			$bodyCache = Mage::getSingleton('superpagespeed/minify')->htmlMinify($bodyCache);
		}

		// Save cache
		$data = array(
			'body' => $bodyCache,
			'dynamicBlocks' => $this->blocksName,
			'dynamicXml' => $xml
		);

		$this->getCache()->save(json_encode($data), $cacheId, $tags, $lifeTime);

		// Replace placeholders with HTML to response page
		$body = str_replace($this->placeholders, $this->blocksHtml, $body);
		$observer->getEvent()->getResponse()->setBody($body);

	}

}