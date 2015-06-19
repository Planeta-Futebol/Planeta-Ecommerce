<?php
class Franchise_Stock_Block_Financialchart extends Mage_Core_Block_Template
{
  public function __construct() {
    parent::__construct();

    $userid = Mage::getSingleton('customer/session')->getCustomer()->getId();
    $post = $this->getRequest()->getPost();
    $collection = array();
    $totalsales = 0;
    $totalcost = 0;
    $totalprofit = 0;
    $i = 0;
    $x = 0;

    if (!empty($post)) {
      $fromdate = $post['fromdate'];
      $todate = $post['todate'];
    } else {
      $todate = new DateTime("now");
      $fromdate = new DateTime("now");
      $fromdate->sub(new DateInterval('P6M')); // 6 months
    }

    $query_sales = Mage::getModel("stock/Saleperpartner")->getCollection()
      ->addFieldToFilter('userid', array('eq'=>$userid))
      ->addFieldToFilter('sale_at', array(
        'from'=>$fromdate->format('d F Y'),
        'to'=>$todate->format('d F Y'),
        'date'=>true
      ));

    if ($query_sales->count()) {
      foreach ($query_sales as $sale) {
        if ($i >= 30) {
          $datetime = new DateTime($date);
          $collection[$x] = array(
            'totalsales'=>$totalsales,
            'totalcost'=>$totalcost,
            'totalprofit'=>$totalprofit,
            'date'=>$datetime->sub(new DateInterval('P15D'))->format('F') // half period (15 days)
          );

          print_r($sale);
          $totalsales = 0;
          $totalcost = 0;
          $totalprofit = 0;
          $i = 0;
          $x++;
        }

        $price_sold = ($sale->getPrice_sold() * $sale->getQty_sold());
        $price_bought = ($sale->getPrice_bought() * $sale->getQty_sold());

        $totalsales += $price_sold;
        $totalcost += $price_bought;
        $totalprofit += ($price_sold - $price_bought);
        $date = $sale->getSale_at();
        $i++;
      }

      if ($i > 0) {
        $datetime = new DateTime($date);
        $collection[$x] = array(
          'totalsales'=>$totalsales,
          'totalcost'=>$totalcost,
          'totalprofit'=>$totalprofit,
          'date'=>$datetime->format('F')
        );
      }
    }

    $this->setChartdata($collection);
  }

  protected function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }

  public function getToolbarBlock() {
    $block = $this->getLayout()->createBlock('stock/toolbar', microtime());
    return $block;
  }

/*
  public function getDefaultDirection() {
    return 'asc';
  }

  public function getSortBy() {
    return 'collection_id';
  }

  public function getMode() {
    return $this->getChild('toolbar')->getCurrentMode();
  }

  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }

  public function getToolbarHtml() {
    return $this->getChildHtml('toolbar');
  }

  public function getColumnCount() {
    return 4;
  }*/
}
