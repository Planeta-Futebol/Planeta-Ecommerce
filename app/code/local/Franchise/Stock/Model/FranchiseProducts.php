<?php
class Franchise_Stock_Model_FranchiseProducts
{
  public function orderSaveEvent() {
    $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    $orderdetail = Mage::getModel('sales/order')->loadByIncrementId($incrementId);

    if(Mage::getSingleton('customer/session')->isLoggedIn()) {
      $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

      if($groupId > 1) {
        $customerData = Mage::getSingleton('customer/session')->getCustomer();
        $customerid = $customerData->getId();
        $wholedata=array("franchise_order"=>$incrementId, "franchise_id"=>$customerid);
        Mage::getModel('stock/product')->saveFranchiseProduct($wholedata);

        return true;
      }
    }
  }
}
?>
