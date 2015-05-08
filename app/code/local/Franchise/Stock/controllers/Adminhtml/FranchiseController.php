<?php
class Franchise_Stock_Adminhtml_FranchiseController extends Mage_Adminhtml_Controller_Action
{
  public function indexAction() {
    $post = $this->getRequest()->getPost();

    // franchise list
    $users = Mage::getModel("customer/customer")->getCollection()
      ->addFieldToFilter('group_id', array('gt'=>1));

    // lastsales data
    $lastsales = Mage::getModel("stock/Saleperpartner")->getCollection()
      ->setOrder('sale_at');
    $lastsales->getSelect()->limit(5);

    // franchise sales data
    $collection = Mage::getModel("stock/Saleperpartner")->getCollection()
      ->setOrder('sale_at');

    if (!empty($post) and $post['franchise']!=0) {
      $collection->addFieldToFilter('userid', array('eq'=>$post['franchise']));
    }

    /* TODO: Make it work
     * $collection->getSelect()->join( array('franchise_data'=> $collection->getTable('customer/customer')),
     *   'franchise_data.entity_id = main_table.userid', array('franchise_data.email'));
     */

    // salesinfo data
    $sales = Mage::getModel("stock/Saleperpartner")->getCollection();
    $totalsales = 0;
    $salescount = 0;

    foreach($sales as $sale) {
      $totalsales += $sale->getPrice_sold();
      $salescount++;
    }

    $salesinfo = array('totalvalue'=>$totalsales, 'average'=>($totalsales/$salescount));
    /* TODO: fill Users, Lastsales and Salesinfo */
    $block = $this->getLayout()->createBlock('stock/admindashboard');
    $block->setUsers($users->load());
    $block->setLastsales($lastsales->load());
    $block->setCollection($collection);
    $block->setSalesinfo($salesinfo);
    $block->preparePager();
    $block->setTemplate("stock/dashboard.phtml");

    $this->_initAction();
    $this->_addContent($block);
    $this->renderLayout();
  }

  protected function _initAction() {
    $this->loadLayout()
      ->_setActiveMenu('package')
      ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'),
        Mage::helper('adminhtml')->__('Item Manager'));

    return $this;
  }
}
?>
