<?php

class Fuza_Local_Model_Observer
{

	public function salesOrderSave($observer)
	{

/*
		$order = $observer->getOrder();
		$quote = $observer->getQuote();

		$data = Mage::app()->getRequest()->getParam('billing');

		$has_changes = false;
		if(isset( $data['rg']) && !empty($data['rg']) ){
			$order->getCustomer()->setRg($data['rg']);
			$has_changes = true;
		}
*/

/*
		if(isset( $data['telephone']) && !empty($data['telephone']) ){
			$order->getCustomer()->setTelephone($data['telephone']);
			$has_changes = true;
		}

		if(isset( $data['fax']) && !empty($data['fax']) ){
			$order->getCustomer()->setFax($data['fax']);
			$has_changes = true;
		}

		if(isset( $data['vat_id']) && !empty($data['vat_id']) ){
			$order->getCustomer()->setTaxvat($data['vat_id']);
			$has_changes = true;
		}
*/
/*
		if ($has_changes){
			$order->getCustomer()->save();
		}
*/
		return $this;
	}

}