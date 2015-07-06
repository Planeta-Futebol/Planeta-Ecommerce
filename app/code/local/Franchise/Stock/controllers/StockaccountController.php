<?php
	require_once 'Mage/Customer/controllers/AccountController.php';

	class Franchise_Stock_StockaccountController extends Mage_Customer_AccountController
	{
		public function indexAction()
		{
			$this->loadLayout();
			$this->renderLayout();
		}

		public function dashboardAction()
		{
			if (($this->getRequest()->isPost()) && (!$this->_validateFormKey())) {
				return $this->_redirect('stock/stockaccount/dashboard/');
			}

			$this->loadLayout(array('default', 'stock_account_dashboard'));

			$this->_initLayoutMessages('customer/session');

			$this->_initLayoutMessages('catalog/session');

			$this->getLayout()
				->getBlock('head')
				->setTitle(Mage::helper('stock')->__('Painel'));

			$this->_getCustomer();
			$this->renderLayout();
		}

		public function myproductslistAction()
		{
			if (($this->getRequest()->isPost()) && (!$this->_validateFormKey())) {
				return $this->_redirect('stock/stockaccount/myproductslist/');
			}

			$this->loadLayout(array('default', 'stock_account_productlist'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()
				->getBlock('head')
				->setTitle(Mage::helper('stock')->__('Meu Estoque'));

			$this->renderLayout();
		}

		public function commissionAction()
		{
			if (($this->getRequest()->isPost()) && (!$this->_validateFormKey())) {
				return $this->_redirect('stock/stockaccount/commission/');
			}

			$this->loadLayout(array('default', 'stock_account_commission'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()
				->getBlock('head')
				->setTitle(Mage::helper('stock')->__('Comissões'));

			$this->renderLayout();
		}

		public function attributeListAction()
		{
			$post = $this->getRequest()->getPost();

			if ($post) {
				$sku = $post['sku'];
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);

				/*
				   * check if product attribute with code all_attribute of textarea
				   * type in attribute_set is created in admin or not.
				   */
				$attributeValue = $product->getResource()
					->getAttribute('all_attribute')
					->getFrontend()
					->getValue($product);

				$result = $attributeValue;
				echo json_encode(array("status" => "success", "res" => $result));
			} else {
				echo json_encode(array("status" => "error", "res" => "There are no attributes."));
			}
		}

		public function saleperpartnerAction()
		{
			if (($this->getRequest()->isPost()) && (!$this->_validateFormKey())) {
				return $this->_redirect('stock/stockaccount/saleperpartner/');
			}

			$this->loadLayout(array('default', 'stock_account_saleperpartner'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()
				->getBlock('head')
				->setTitle(Mage::helper('stock')->__('Relatório de vendas'));

			$this->renderLayout();
		}

		public function saveSalesReportAction()
		{
			$post = $this->getRequest()->getPost();

			if ($post) {
				$result = Mage::getModel('stock/Saleperpartner')->saveSaleReport($post);
				echo json_encode(array("status" => "success", "res" => $result));
			} else {
				echo json_encode(array("status" => "error", "res" => "Please refresh the page and try again."));
			}
		}

		public function financialchartAction()
		{
			if (($this->getRequest()->isPost()) && (!$this->_validateFormKey())) {
				return $this->_redirect('stock/stockaccount/financialchart/');
			}

			$this->loadLayout(array('default', 'stock_account_financialchart'));
			$this->_initLayoutMessages('customer/session');
			$this->_initLayoutMessages('catalog/session');
			$this->getLayout()
				->getBlock('head')
				->setTitle(Mage::helper('stock')->__('Relatório Financeiro'));

			$this->renderLayout();
		}
	}
