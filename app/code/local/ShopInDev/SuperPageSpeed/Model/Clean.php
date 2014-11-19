<?php
/**
 * ShopInDev
 *
 * @category    ShopInDev
 * @package     ShopInDev_SuperPageSpeed
 * @copyright   Copyright (c) 2014 ShopInDev
 * @license     http://opensource.org/licenses/GPL-3.0 GNU General Public License (GPL)
 */

class ShopInDev_SuperPageSpeed_Model_Clean {

	/**
	 * Retrive module cache
	 * @return Mage_Core_Model_Cache
	 */
	public function getCache(){
		return Mage::app()->getCacheInstance();
	}

	/**
	 * Clean cache tags
	 * @param array $tags
	 * @return void
	 */
	public function cleanCache($tags){

		if( $tags ){
			$this->getCache()->clean($tags);
		}

	}

	/**
	 * Clean cache on product save
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function catalogProductSaveAfter($observer){

		if( !Mage::helper('superpagespeed')->isCacheActive() ){
			return;
		}

		$tags = array();
		$product = $observer->getEvent()->getProduct();

		if( $product->getId() ){
			$tags[] = Mage_Catalog_Model_Product::CACHE_TAG. '_'. $product->getId();

			$categories = $product->getCategoryIds();
			foreach( $categories as $categoryId ){
				$tags[] = Mage_Catalog_Model_Category::CACHE_TAG. '_'. $categoryId;
			}
		}

		$this->cleanCache($tags);
	}

	/**
	 * Clean cache on category save
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function catalogCategorySaveAfter($observer){

		if( !Mage::helper('superpagespeed')->isCacheActive() ){
			return;
		}

		$tags = array();
		$category = $observer->getEvent()->getCategory();

		if( $category->getId() ){
			$tags[] = Mage_Catalog_Model_Category::CACHE_TAG. '_'. $category->getId();
		}

		$this->cleanCache($tags);
	}

	/**
	 * Clean cache on page save
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function cmsPageSaveAfter($observer){

		if( !Mage::helper('superpagespeed')->isCacheActive() ){
			return;
		}

		$tags = array();
		$page = $observer->getEvent()->getObject();

		if( $page->getId() ){
			$tags[] = Mage_Cms_Model_Page::CACHE_TAG. '_'. $page->getId();
		}

		$this->cleanCache($tags);
	}

	/**
	 * Clean cache on inventory change
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function catalogInventoryStockItemSaveAfter($observer){

		if( !Mage::helper('superpagespeed')->isCacheActive() ){
			return;
		}

		$tags = array();
		$item = $observer->getEvent()->getItem();
		$product = Mage::getModel('catalog/product')->load( $item->getProductId() );

		if( $product->getId() ){
			$tags[] = Mage_Catalog_Model_Product::CACHE_TAG. '_'. $product->getId();

			$categories = $product->getCategoryIds();
			foreach( $categories as $categoryId ){
				$tags[] = Mage_Catalog_Model_Category::CACHE_TAG. '_'. $categoryId;
			}
		}

		$this->cleanCache($tags);

	}

	/**
	 * Clean cache on checkout success
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function checkoutControllerSuccessAction($observer){

		if( !Mage::helper('superpagespeed')->isCacheActive() ){
			return;
		}

		$tags = array();
		$orderId = current($observer->getOrderIds());
        $order = Mage::getModel('sales/order')->load($orderId);
		$items = $order->getAllItems();

		foreach( $items as $item ){

			$product = Mage::getModel('catalog/product')->load( $item->getProductId() );

			if( !$product->getId() ){
				continue;
			}

			$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct( $product->getId() );

			if( !$stockItem->getId() OR !$stockItem->canSubtractQty() ){
				continue;
			}

			$tags[] = Mage_Catalog_Model_Product::CACHE_TAG. '_'. $product->getId();

			$categories = $product->getCategoryIds();
			foreach( $categories as $categoryId ){
				$tags[] = Mage_Catalog_Model_Category::CACHE_TAG. '_'. $categoryId;
			}
		}

		$this->cleanCache($tags);

	}

	/**
	 * Automatic clean all invalid cache types
	 * Run via cronjob
	 * @return void
	 */
	public function cleanAllInvalidCache(){

		$invalidatedCacheTypes = $this->getCache()->getInvalidatedTypes();

		foreach( $invalidatedCacheTypes as $type ){
			$this->getCache()->cleanType( $type->getId() );
		}

	}

}