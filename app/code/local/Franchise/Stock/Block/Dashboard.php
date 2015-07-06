<?php

	class Franchise_Stock_Block_Dashboard extends Mage_Core_Block_Template
	{
		public function __construct()
		{
			parent::__construct();

			$nameCustomer = Mage::getSingleton('customer/session')->getCustomer()->getName();


			/** @var Franchise_Stock_Model_Dashboard $dashboard */
			$dashboard = Mage::getModel('stock/dashboard');

			$this->setData('nameCustomer', $nameCustomer);
			$this->setData('nameFranchise', $dashboard->getNameFranchise());
			$this->setData('fullPotentialSales', $dashboard->getFullPotentialSales());
			$this->setData('fullSalesPrice', $dashboard->getFullSalesPrice());
			$this->setData('fullProfits', $dashboard->getFullProfits());
			$this->setData('productStockItem', $dashboard->getProductStockItem());
			$this->setData('productMoreSold', $dashboard->getProductMoreSold());
			$this->setData('productMoreProfit', $dashboard->getProductMoreProfit());

		}

		protected function _prepareLayout()
		{
			parent::_prepareLayout();
			return $this;
		}
	}