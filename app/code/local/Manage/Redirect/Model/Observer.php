<?php
/**
 * This Observer get admin_session_user_login_success event.
 *
 * @category   Manage
 * @package    Manage_Redirect
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Redirect_Model_Observer
{

    /**
     * This method is called when admin_session_user_login_success event is triggered.
     * If user that logged is a representative (special user admin), he is redirected for sales_order_create
     * This method is designed to prevent the representative to see the sales order list
     *
     */
    public function adminRedirect()
    {
        /** @var Manage_Redirect_Helper_Data $redirect */
        $redirect = Mage::helper('redirect');

        if($redirect->isRepresentative()){

            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("adminhtml/sales_order_create/index"));
            Mage::app()->getResponse()->sendResponse();
            //ensures that redirected
            die;
        }
    }
}