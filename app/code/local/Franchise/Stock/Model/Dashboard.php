<?php

	/**
	 * Responsible for mixing data about products, sales, credits and commissions.
	 * class non-entity model, only makes use of pre-established entities
	 * to collect the relevant data to be displayed on the general panel franchisee.
	 *
	 * Importantly, this class uses in some of his methods call the native
	 * functions of MySql data pack, if the bank is changed,
	 * the methods must be rewritten to ensure full operation.
	 *
	 *
	 * Class Franchise_Stock_Model_Dashboard
	 *
	 * @author Ronildo dos Santos
	 * @version 1.0
	 */
	class Franchise_Stock_Model_Dashboard
	{
		/**
		 * Customer references the ID currently logged in.
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
		 * It represents an interval of days, today.
		 *
		 * @var int
		 */
		const INTERVAL_TODAY = 0;

		/**
		 * It represents a seven-day interval
		 *
		 * @var int
		 */
		const INTERVAL_SEVEN_DAYS = 7;

		/**
		 * It represents a thirty-day interval
		 *
		 * @var int
		 */
		const INTERVAL_THIRTY_DAYS = 30;

		/**
		 * Retrieves the User ID logged in, your name and Products in stock of his franchise.
		 *
		 */
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
				$priceSalesProduct = $product->getMsrp();
				$quantityProduct = (int) $stockItem->getData('qty');

				$fullPotentialSales += $priceSalesProduct * $quantityProduct;
			}

			return $fullPotentialSales;
		}

		/**
		 * Returns the total sales value for a certain number of days,
		 * if $intervalDays is omitted returns the total value of sales.
		 *
		 * @param null|int $intervalDays
		 * @return int
		 */
		public function getFullSalesPrice( $intervalDays = null )
		{
			$collection = Mage::getModel('stock/saleperpartner')->getCollection()
				->addFieldToFilter('userid', array('eq' => $this->customerId));

			if(!is_null($intervalDays)) {
				$collection->getSelect()->where("sale_at BETWEEN date(NOW()) AND DATE_SUB(date(NOW()), INTERVAL $intervalDays DAY)");
			}

			$fullSalesPrice = 0;
			foreach ($collection as $product){

				$salesPrice = $product->getPrice_sold();
				$quantity = $product->getData('qty_sold');

				$fullSalesPrice += $salesPrice * $quantity;
			}

			return $fullSalesPrice;
		}


		/**
		 * Retrieves the profits in a certain number of days, if $intervalDays
		 * is omitted, retrieves the total profit.
		 *
		 * @param null|int $intervalDays
		 * @return int
		 */
		public function getFullProfits( $intervalDays = null )
		{
			$collection = Mage::getModel('stock/saleperpartner')->getCollection()
				->addFieldToFilter('userid', array('eq' => $this->customerId));


			if(!is_null($intervalDays)) {
				$collection->getSelect()->where("sale_at BETWEEN date(NOW()) AND DATE_SUB(date(NOW()), INTERVAL $intervalDays DAY)");
			}

			$fullProfits = 0;
			foreach ($collection as $product){

				$productPrice = $product->getPrice_bought();
				$salesPrice = $product->getPrice_sold();
				$quantity = $product->getData('qty_sold');
				$profit = ($salesPrice - $productPrice) * $quantity;

				$fullProfits += $profit;

			}

			return $fullProfits;
		}


		/**
		 * Retrieves the franchisee's products with the lowest stock,
		 * assembles an array of name and quantity and returns to the caller.
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
		 * Returns the five best-selling products in a certain number of days,
		 * if $intervalDays is omitted, returns the five
		 * best-selling products of all the period.
		 *
		 * @return array
		 */
		public function getProductMoreSold( $intervalDays = null )
		{
			$collection = Mage::getModel('stock/saleperpartner')->getCollection();

			$collection->addFieldToFilter('userid', array('eq' => $this->customerId));

			if(!is_null($intervalDays)) {
				$collection->getSelect()->where("sale_at BETWEEN date(NOW()) AND DATE_SUB(date(NOW()), INTERVAL $intervalDays DAY)");
			}

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
		 * Returns the five products that generate more profits in a certain number of days,
		 * if $intervalDays is omitted, returns the five products that generate more profits.
		 *
		 * @return array
		 */
		public function getProductMoreProfit( $intervalDays = null )
		{
			$collection = Mage::getModel('stock/saleperpartner')->getCollection();

			$collection->addFieldToFilter('userid', array('eq' => $this->customerId));

			if(!is_null($intervalDays)) {
				$collection->getSelect()->where("sale_at BETWEEN date(NOW()) AND DATE_SUB(date(NOW()), INTERVAL $intervalDays DAY)");
			}

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
		 * Returns the total purchases based on concluded requests for a range of days,
		 * if $intervalDays is omitted, returns the total purchases.
		 *
		 * @param null|int $intervalDays
		 * @return int
		 */
		public function getFullPurchases( $intervalDays = null )
		{
			$collection = Mage::getModel('sales/order')->getCollection();

			$collection->addFieldToFilter('customer_id', array('eq' => $this->customerId));

			if(!is_null($intervalDays)) {
				$collection->getSelect()->where("created_at BETWEEN date(NOW()) AND DATE_SUB(date(NOW()), INTERVAL $intervalDays DAY)");
			}

			$fullPurchases = 0;
			foreach($collection as $purchases){
				$fullPurchases += $purchases->getData('grand_total');
			}

			return $fullPurchases;
		}

		/**
		 * Returns the five largest comições generators for a range of days, if
		 * $intervalDays is omitdo returns the five largest comições generators,
		 *
		 * @param null $intervalDays
		 * @return array
		 */
		public function getGenerateMoreCommission( $intervalDays = null )
		{
			$existedAccount = Mage::getModel('affiliateplus/account')->loadByCustomerId($this->customerId);

			$collection = Mage::getModel('affiliateplus/transaction')->getCollection();
			$collection->addFieldToFilter('account_id', array('in' => $existedAccount->getId()));

			if(!is_null($intervalDays)) {
				$collection->getSelect()->where("created_time BETWEEN date(NOW()) AND DATE_SUB(date(NOW()), INTERVAL $intervalDays DAY)");
			}

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

		/**
		 * Returns the franchise and retail coupons.
		 *
		 * @return array
		 */
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
		 * Returns the total of the affiliate credits.
		 *
		 * @return string
		 */
		public function getCreditAffiliatePlus()
		{
			$balance = Mage::helper('affiliateplus/account')->getAccount()->getBalance();
			$balance = Mage::app()->getStore()->convertPrice($balance);

			$checkout = Mage::getSingleton('checkout/session');
			if ($checkout->getAffiliateCredit() > 0) {
				$balance -= $checkout->getAffiliateCredit();
			}

			return Mage::app()->getStore()->formatPrice($balance);
		}

		/**
		 * Returns the name of franchise
		 *
		 * @return string
		 */
		public function getNameFranchise()
		{
			return $this->nameFranchise;
		}
	}