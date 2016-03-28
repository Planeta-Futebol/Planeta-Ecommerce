<?php
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';

/**
 * This Controller extends OrderController core to prevent that user representative
 * see sales order list.
 *
 * @category   Manage
 * @package    Manage_Redirect
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Redirect_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController
{

    /**
     * Call default action, and redirect
     * to sales_order_create if user logged is a representative.
     */
    public function indexAction()
    {
        parent::indexAction();

        /** @var Manage_Redirect_Helper_Data $redirect */
        $redirect = Mage::helper('redirect');

        if($redirect->isRepresentative()){
            $this->_redirect("*/sales_order_create/index");
        }
    }
}
