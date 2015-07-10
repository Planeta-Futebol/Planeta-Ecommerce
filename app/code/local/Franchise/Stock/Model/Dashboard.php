<?php

	/**
	 * Responsible for mixing data about products, sales, credits and commissions.
	 * class non-entity model, only makes use of pre-established entities
	 * to collect the relevant data to be displayed on the general panel franchisee.
	 *
	 *
	 * Class Franchise_Stock_Model_Dashboard
	 *
	 * @author Ronildo dos Santos
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
		 * Retrieves the franchisee's products with the lowest stock,
		 * assembles an array of name and quantity and returns to the caller.
		 *
		 *
		 * @return array
		 */
		public function getProductStockItem()
		{
			$collection = Mage::getModel('stock/product')->getCollection();

			$collection->addFieldToFilter('userid', array('eq' => $this->customerId));

			$collection->getSelect()
				->join(
					array('stock_item' => 'cataloginventory_stock_item'),
					'main_table.stockproductid = stock_item.product_id'
				)
				->order('qty')
				->limit(5);

			$arrProductStockItem = array();
			$key = 0;

			foreach($collection as $product){
				$catalogProduct = Mage::getModel('catalog/product')->load($product->getProduct_id());

				$arrProductStockItem[$key] = array(
					'name' => $catalogProduct->getData('name'),
					'quantity' => (int) $product->getData('qty')

				);

				$key++;
			}

			return $arrProductStockItem;
		}

		/**
		 * Returns the top selling products
		 *
		 * @return array
		 */
		public function getProductMoreSold()
		{
			$collection = Mage::getModel('stock/saleperpartner')->getCollection();

			$collection->addFieldToFilter('userid', array('eq' => $this->customerId));

			$collection->getSelect()
				->join(
					array('stock_item' => 'cataloginventory_stock_item'),
					'main_table.stockprodid = stock_item.product_id'
				)
				->order('qty_sold DESC')
				->limit(5);

			$arrProductMoreSold = array();
			$key = 0;

			foreach($collection as $product){
				$catalogProduct = Mage::getModel('catalog/product')->load($product->getProduct_id());

				$arrProductMoreSold[$key] = array(
					'name' => $catalogProduct->getData('name'),
					'quantity' => (int) $product->getData('qty_sold')

				);

				$key++;
			}

			return $arrProductMoreSold;
		}


		/**
		 * Returns products that generate more profits
		 *
		 * @return array
		 */
		public function getProductMoreProfit()
		{
			$collection = Mage::getModel('stock/saleperpartner')->getCollection();

			$collection->addFieldToFilter('userid', array('eq' => $this->customerId));

			$collection->getSelect()
				->join(
					array('stock_item' => 'cataloginventory_stock_item'),
					'main_table.stockprodid = stock_item.product_id',
					array(
						'profit' =>'((price_sold - price_bought) * qty_sold)',
						'stock_item.*'
					)
				)
				->order('profit DESC')
				->limit(5);

			$arrProductMoreProfit = array();
			$key = 0;

			foreach($collection as $product){
				$catalogProduct = Mage::getModel('catalog/product')->load($product->getProduct_id());

				$arrProductMoreProfit[$key] = array(
					'name' => $catalogProduct->getData('name'),
					'profit' => (float) $product->getData('profit')

				);

				$key++;
			}

			return $arrProductMoreProfit;
		}


		/**
		 * Returns the total purchases based on firm orders.
		 *
		 * @return float
		 */
		public function getFullPurchases()
		{
			$collection = Mage::getModel('sales/order')->getCollection();

			$collection->addFieldToFilter('customer_id', array('eq' => $this->customerId));

			$collection->getSelect();

			$fullPurchases = 0;
			foreach($collection as $purchases){
				$fullPurchases += $purchases->getData('grand_total');
			}

			return $fullPurchases;
		}

		public function getGenerateMoreCommission()
		{
			$existedAccount = Mage::getModel('affiliateplus/account')->loadByCustomerId($this->customerId);

			$collection = Mage::getModel('affiliateplus/transaction')->getCollection();
			$collection->addFieldToFilter('account_id', array('in' => $existedAccount->getId()));

			$collection->getSelect()
				->order('commission DESC')
				->limit(5);


			$arrFullCommission = array();
			$key = 0;
			foreach($collection as $commissions) {
				$commission = $commissions->getCommission();
				$customer = Mage::getModel('customer/customer')->load($commissions->getCustomer_id());

				$fullName = $customer->getData('firstname') . " " . $customer->getData('lastname');

				$arrFullCommission[$key] = array(
					'commission' => $commission,
					'name' => $fullName
				);
			}

			return $arrFullCommission;
		}

		public function getCouponsAffiliate()
		{
			$existedAccount = Mage::getModel('affiliateplus/account')->loadByCustomerId($this->customerId);

			$collection = Mage::getModel('affiliatepluscoupon/coupon')->getCollection();
			$collection->addFieldToFilter('account_id', array('in' => $existedAccount->getId()));
			$collection->addFieldToFilter('program_id', array('neq' => 0 ));

			$collection->getSelect()->order('program_id');

			$arrCouponAffiliate = array();
			$key = 0;

			foreach( $collection as $coupon ){
				$arrCouponAffiliate[$key] = $coupon->getCoupon_code();
				$key++;
			}

			return $arrCouponAffiliate;
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