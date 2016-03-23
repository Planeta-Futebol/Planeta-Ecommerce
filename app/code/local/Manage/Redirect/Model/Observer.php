<?php

class Manage_Redirect_Model_Observer
{
    public function adminRedirect()
    {
        $role = Mage::getSingleton('admin/session')
            ->getUser()
            ->getRole()
            ->getData();

        if ($role['role_name'] == 'representante') {

            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order_create/index"));
            Mage::app()->getResponse()->sendResponse();
            die;
        }
    }
}