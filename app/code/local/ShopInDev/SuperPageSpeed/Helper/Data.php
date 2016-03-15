<?php
/**
 * ShopInDev
 *
 * @category    ShopInDev
 * @package     ShopInDev_SuperPageSpeed
 * @copyright   Copyright (c) 2014 ShopInDev
 * @license     http://opensource.org/licenses/GPL-3.0 GNU General Public License (GPL)
 */

class ShopInDev_SuperPageSpeed_Helper_Data extends Mage_Core_Helper_Abstract {

	const CACHE_TAG = 'SUPER_PAGE_SPEED';

	const XML_PATH_USE_MULTILOCATION = 'system/superpagespeed/use_multilocation';
	const XML_PATH_USE_MULTITHEME = 'system/superpagespeed/use_multitheme';
	const XML_PATH_DYNAMIC_BLOCKS = 'system/superpagespeed/dynamic_blocks';
	const XML_PATH_BLOCK_URLS = 'system/superpagespeed/block_urls';
	const XML_PATH_SESSION_TYPES = 'system/superpagespeed/session_types';
	const XML_PATH_SESSION_PARAMS = 'system/superpagespeed/session_params';
	const XML_PATH_DIFF_VISITS = 'system/superpagespeed/diff_visits';
	const XML_PATH_MINIFY_HTML = 'system/superpagespeed/minify_html';
	const XML_PATH_CACHE_AJAX = 'system/superpagespeed/cache_ajax';
	const XML_PATH_CACHE_LIFETIME = 'system/superpagespeed/cache_lifetime';
	const XML_PATH_SHOW_DEBUG = 'system/superpagespeed/show_debug';

	private $config = array();

	/**
	 * Return if is cache type active
	 * @return boolean
	 */
	public function isCacheActive(){
		return Mage::app()->useCache('superpagespeed');
	}

	/**
	 * Return if is on secure URL
	 * @return boolean
	 */
	public function isSecure(){
		return (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] !== 'off')
			   OR $_SERVER['SERVER_PORT'] == 443;
	}

	/**
	 * Return if the request is in Ajax
	 * @return boolean
	 */
	public function isAjax(){

		if( Mage::app()->getRequest()->isXmlHttpRequest() ){
			return TRUE;
		}

		if( Mage::app()->getRequest()->getParam('ajax')
			OR Mage::app()->getRequest()->getParam('isAjax') ){
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Retrieve store config
	 * Wrapper to avoid many requests
	 * @param string $path
	 * @param boolean $isArray
	 * @return mixed
	 */
	public function getConfig($path, $isArray = FALSE){

		if( isset( $this->config[ $path ] ) ){
			return $this->config[ $path ];
		}

		$value = (string) Mage::getStoreConfig($path);

		if( $isArray ){
			$value = ($value) ?
					 preg_split('/[\n\r]+/', $value, -1, PREG_SPLIT_NO_EMPTY) :
					 array();
		}

		$this->config[ $path ] = $value;

		return $this->config[ $path ];
	}

	/**
	 * Return if can do cache for that URL
	 * @return boolean
	 */
	public function canDoCache(){
		return $this->isCacheActive() AND $this->canCacheUrl();
	}

	/**
	 * Return if can show debug blocks
	 * Visible only for devs
	 * @return boolean
	 */
	public function canShowDebug(){
		return $this->getConfig( self::XML_PATH_SHOW_DEBUG )
		       AND Mage::helper('core')->isDevAllowed();
	}

	/**
	 * Return if that block can be cached
	 * @param string $blockName
	 * @return boolean
	 */
	public function canCacheBlock( $blockName ){

		$dynamicBlocks = (array) $this->getConfig( self::XML_PATH_DYNAMIC_BLOCKS, TRUE );

		if( in_array($blockName, $dynamicBlocks) ){
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Return if module can cache ajax requests
	 * @return boolean
	 */
	public function canCacheAjax(){
		return (boolean) $this->getConfig( self::XML_PATH_CACHE_AJAX );
	}

	/**
	 * Return if module can diff cache for each visitor type
	 * @return boolean
	 */
	public function canDiffVisits(){
		return (boolean) $this->getConfig( self::XML_PATH_DIFF_VISITS );
	}

	/**
	 * Return if module can minify HTML code
	 * @return boolean
	 */
	public function canMinifyHTML(){
		return (boolean) $this->getConfig( self::XML_PATH_MINIFY_HTML );
	}

	/**
	 * Return if that page can be cached
	 * @return boolean
	 */
	public function canCacheUrl(){

		// Only cache GET requests
		if( Mage::app()->getRequest()->getMethod() != 'GET' ){
			return FALSE;
		}

		// Do not cache uncached requests
		if( Mage::app()->getRequest()->getParam('no_cache') ){
			return FALSE;
		}

		// Do not cache ajax request?
		if( $this->isAjax() AND !$this->canCacheAjax() ){
			return FALSE;
		}

		// Do not cache blocked URLs
		$blockUrls = (array) $this->getConfig( self::XML_PATH_BLOCK_URLS, TRUE );

		// If can cache all URLs
		if( !$blockUrls ){
			return TRUE;
		}

		// Check if can cache the request
		$url = (string) $_SERVER['REQUEST_URI'];
		$canCache = TRUE;

		foreach( $blockUrls as $key => $value ){

			if( stripos($url, $value) !== FALSE ){
				$canCache = FALSE;
				break;
			}

		}

		return $canCache;
	}

	/**
	 * Return if the store use multi-location
	 * @return boolean
	 */
	public function useMultiLocation(){
		return (boolean) $this->getConfig( self::XML_PATH_USE_MULTILOCATION );
	}

	/**
	 * Return if the store use multi-theme
	 * @return boolean
	 */
	public function useMultiTheme(){
		return (boolean) $this->getConfig( self::XML_PATH_USE_MULTITHEME );
	}

	/**
	 * Retrieve the cache tags
	 * Almost every tag clean the cache for page speed
	 * @return array
	 */
	public function getCacheTags(){

		$tags = array();
		$tags[] = self::CACHE_TAG;
		$tags[] = Mage_Core_Model_App::CACHE_TAG;
		$tags[] = Mage_Core_Model_Config::CACHE_TAG;
		$tags[] = Mage_Core_Model_Layout_Update::LAYOUT_GENERAL_CACHE_TAG;
		$tags[] = Mage_Core_Model_Translate::CACHE_TAG;
		$tags[] = Mage_Cms_Model_Block::CACHE_TAG;

		$controllerName = Mage::app()->getRequest()->getControllerName();
		$id = Mage::app()->getRequest()->getParam('id');

		if( $controllerName == 'page' ){
			$id = Mage::app()->getRequest()->getParam('page_id');
			$tags[] = Mage_Cms_Model_Page::CACHE_TAG;
			$tags[] = Mage_Cms_Model_Page::CACHE_TAG. '_'. $id;

		}elseif( $controllerName == 'category' ){
			$tags[] = Mage_Catalog_Model_Category::CACHE_TAG;
			$tags[] = Mage_Catalog_Model_Category::CACHE_TAG. '_'. $id;

		}elseif( $controllerName == 'product' ){
			$tags[] = Mage_Catalog_Model_Product::CACHE_TAG;
			$tags[] = Mage_Catalog_Model_Product::CACHE_TAG. '_'. $id;
		}

		return $tags;
	}

	/**
	 * Retrieve the session types to process on cached request
	 * @return array
	 */
	public function getSessionTypes(){
		return (array) $this->getConfig( self::XML_PATH_SESSION_TYPES, TRUE );
	}

	/**
	 * Retrieve catalog session params
	 * Need for some modules
	 * @return array
	 */
	public function getSessionParams(){

		$params = array();
		$sessionParams = (array) $this->getConfig( self::XML_PATH_SESSION_PARAMS, TRUE );

		if( !$sessionParams ){
			return $params;
		}

		$catalogSession = Mage::getSingleton('catalog/session');

		foreach( $sessionParams as $param ){
			if( $data = $catalogSession->getData($param) ){
				$params[] = 'session_'. $param. '_'. $data;
			}
		}

		return $params;
	}

	/**
	 * Retrieve cache lifetime
	 * @return int
	 */
	public function getCacheLifetime(){
		return (int) $this->getConfig( self::XML_PATH_CACHE_LIFETIME ) * 60;
	}

	/**
	 * Retrive the request ID for the page
	 * @return string
	 */
	public function getCacheRequestId(){

		$url = $_SERVER['REQUEST_URI'];
		$url = str_replace('index.php/', '', $url);
		$url = trim($url, '/');

		$items = array();
		$items[] = $url;
		$items[] = ( $this->isSecure() ) ? 'https' : 'http';

		if( $this->useMultiLocation() ){
			$items[] = Mage::app()->getLocale()->getLocaleCode();
			$items[] = Mage::app()->getStore()->getCurrentCurrencyCode();
		}

		if( $this->useMultiTheme() ){
			$items[] = Mage::getDesign()->getTheme('template');
			$items[] = Mage::getDesign()->getTheme('skin');
			$items[] = Mage::getDesign()->getTheme('layout');
			$items[] = Mage::getDesign()->getTheme('frontend');
		}

		if( $this->canDiffVisits() ){

			if( Mage::getSingleton('customer/session')->isLoggedIn() ){
				$items[] = 'logged';
				$items[] = 'group_'. Mage::getSingleton('customer/session')
										   ->getCustomerGroupId();
			}

		}

		$items = array_merge($items, $this->getSessionParams());

		$id = implode('_', $items);
		$id = strtolower($id);
		$id = md5($id);

		$id = self::CACHE_TAG. $id;

		return $id;
	}

}