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

		/**
		 * Represents total franchisee's profits.
		 *
		 * @var int
		 */
		private $fullProfits = 0;

		/**
		 * Represents total franchisee's sales.
		 *
		 * @var int
		 */
		private $fullSalesPrice = 0;

		public function __construct()
		{

			$customer = Mage::getSingleton('customer/session')->getCustomer();

			$this->customerId = $customer->getId();

			$this->productFranchise = Mage::getModel('stock/product');

			$groupCustomerId = Mage::getSingleton('customer/session')->getCustomerGroupId();

			$typeFranchise = Mage::getModel("customer/group")->load($groupCustomerId, 'customer_group_id');
			$this->nameFranchise = $typeFranchise->getData('customer_group_code');

			$this->initFullSalesAndProfits();

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
				$priceSalesProduct = $product->getMsrp();
				$quantityProduct = (int) $stockItem->getData('qty');

				$fullPotentialSales += $priceSalesProduct * $quantityProduct;
			}

			return $fullPotentialSales;
		}


		/**
		 * Retrieves all sales of the franchisee currently logged in.
		 * Calculates profits and then sets the $fullSalesPrice figures and $fullProfits.
		 *
		 * @access private
		 * @return void
		 */
		private function initFullSalesAndProfits()
		{
			$products = Mage::getModel('stock/saleperpartner')->getCollection()
				->addFieldToFilter('userid', array('eq' => $this->customerId));

			foreach ($products as $product){

				$productPrice = $product->getPrice_bought();
				$salesPrice = $product->getPrice_sold();
				$quantity = $product->getData('qty_sold');
				$profit = ($salesPrice - $productPrice) * $quantity;

				$this->fullProfits += $profit;
				$this->fullSalesPrice += $salesPrice * $quantity;
			}
		}

		/**
		 * @return int
		 */
		public function getFullSalesPrice()
		{

			return $this->fullSalesPrice;
		}

		/**
		 * @return int
		 */
		public function getFullProfits()
		{
			return $this->fullProfits;
		}

		/**
		 * @return string
		 */
		public function getNameFranchise()
		{
			return $this->nameFranchise;
		}

	}