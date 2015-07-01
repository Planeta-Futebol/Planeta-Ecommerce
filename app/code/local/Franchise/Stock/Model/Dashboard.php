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
	 * It is a product entity model franchisee
	 *
	 * @var Franchise_Stock_Model_Product
	 */
	private $productFranchise = null;

	public function __construct()
	{

		$this->customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$this->productFranchise = Mage::getModel('stock/product');

	}

	public function getFullPotentialSales()
	{

	}

}