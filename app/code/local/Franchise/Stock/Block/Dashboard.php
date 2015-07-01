<?php

	class Franchise_Stock_Block_Dashboard extends Mage_Core_Block_Template
	{
		public function __construct()
		{
			parent::__construct();

			$nameCustomer = Mage::getSingleton('customer/session')->getCustomer()->getName();

			/** @var Franchise_Stock_Model_Dashboard $model */
			$model = Mage::getModel('stock/dashboard');

			$model->getFullPotentialSales();

			$this->setData('nameCustomer', $nameCustomer);


		}

		protected function _prepareLayout()
		{
			parent::_prepareLayout();
			return $this;
		}
	}
