<?php
class Franchise_Customer_Model_Observer
{
    public function setOptionFranchise( Varien_Event_Observer $observer )
    {
        $customer = $observer->getEvent()->getCustomer();

    }
}