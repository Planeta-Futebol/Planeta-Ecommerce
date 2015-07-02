<?php

	/**
	 * Responsible for mixing data about products, sales, credits and commissions.
	 * class non-entity model, only makes use of pre-established entities
	 * to collect the relevant data to be displayed on the general panel franchisee.
	 *
	 *
	 * Class Franchise_Stock_Model_Dashboard
	 */
	class Franchise_Stock_Model_Dashboard
	{
		/**
		 * Customer references the ID currently logged in.
		 *
		 *
		 * @var int
		 */
		private $customerId;

		/**
		 * It is a product entity model franchisee.
		 *
		 * @var Franchise_Stock_Model_Product
		 */
		private $productFranchise = null;

		/**
		 * Franchise name of the customer currently logged in.
		 *
		 * @var string
		 */
		private $nameFranchise;

		public function __construct()
		{

			$customer = Mage::getSingleton('customer/session')->getCustomer();

			$this->customerId = $customer->getId();

			$this->productFranchise = Mage::getModel('stock/product');

			$groupCustomerId = Mage::getSingleton('customer/session')->getCustomerGroupId();

			$typeFranchise = Mage::getModel("customer/group")->load($groupCustomerId, 'customer_group_id');
			$this->nameFranchise = $typeFranchise->getData('customer_group_code');

		}

		/**
		 * Total value of current inventory based on suggested retail price.
		 *
		 * @return int
		 */
		public function getFullPotentialSales()
		{
			$products = $this->productFranchise->getFrCollection($this->customerId);

			$fullPotentialSales = 0;

			foreach ($products as $product){
				$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
				$priceProduct = $product->getSpecialPrice();
				$quantityProduct = (int) $stockItem->getData('qty');

				$fullPotentialSales += $priceProduct * $quantityProduct;
			}

			return $fullPotentialSales;
		}

		/**
		 * @return string
		 */
		public function getNameFranchise()
		{
			return $this->nameFranchise;
		}

	}