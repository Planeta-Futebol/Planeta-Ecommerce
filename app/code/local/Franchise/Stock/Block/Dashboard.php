<?php

	class Franchise_Stock_Block_Dashboard extends Mage_Core_Block_Template
	{
		public function __construct()
		{
			parent::__construct();

			$nameCustomer = Mage::getSingleton('customer/session')->getCustomer()->getName();


			/** @var Franchise_Stock_Model_Dashboard $dashboard */
			$dashboard = Mage::getModel('stock/dashboard');

			$dashboard->getFullPotentialSales();

			$this->setData('nameCustomer', $nameCustomer);
			$this->setData('nameFranchise', $dashboard->getNameFranchise());


		}

		protected function _prepareLayout()
		{
			parent::_prepareLayout();
			return $this;
		}
	}