<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fuza_Bling_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{

	public function alocaNfAction()
	{

		if (!Mage::getStoreConfig('fuza_bling/geral/enable_bling')) {
			$this->_getSession()->addError('Módulo Fuza Bling está desabilitado.');
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view/", array('order_id' => $order_id)));
		}

		$order_id = $this->getRequest()->getParam('order_id');

		$order = Mage::getModel('sales/order')->load($order_id);

		if(!$order->getId()){
			$this->_getSession()->addError('Número de Pedido não existe.');
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view/", array('order_id' => $order_id)));
		}


		if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE || $order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING){

			if ($order->getGrand_total() > 0) {

				// verificar se já nao existe para não criar novamente
				$blingNF = Mage::getModel('fuza_bling/blingnf')->load( $order->getId(), 'order_id' );
				$nf = $blingNF->getData();
				
				// inicia processo de geração de NFe
				if (!$nf):
					$objNf = Mage::getModel('fuza_bling/blingnf');
					$objNf->setOrderId($order_id);
					$objNf->setStatus('created');
					$objNf->setCreatedAt(Mage::getModel('core/date')->date());
					$objNf->save();
/*
					if ($order->getShippingAddress()->getRegion() == 'Amazonas'):
						$comment = '#ATENÇÃO: PEDIDO DEVE TER NOTA FISCAL IMPRESSA E ANEXADA NO LADO EXTERNO DA EMBALAGEM';
						$order->addStatusHistoryComment($comment, false)->setIsCustomerNotified(false);
						$order->save();
					endif;
*/
					$this->_getSession()->addSuccess('NFe iniciada.');
				endif;

			} else {
				$this->_getSession()->addError('Pedido possui valor total igual a zero, impossível gerar NFe.');
			}
		} else {
			$this->_getSession()->addError('Pedido não está completo para poder gerar NFe.');
		}
		
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/view/", array('order_id' => $order_id)));
	}

}