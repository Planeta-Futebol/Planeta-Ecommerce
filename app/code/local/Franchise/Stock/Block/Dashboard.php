<?php

class Franchise_Stock_Block_Dashboard extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        $nameCustomer = Mage::getSingleton('customer/session')->getCustomer()->getName();


        /** @var Franchise_Stock_Model_Dashboard $dashboard */
        $dashboard = Mage::getModel('stock/dashboard');

        $this->setData('nameCustomer', $nameCustomer);
        $this->setData('franchiseType', $dashboard->getFranchiseType());
        $this->setData('fullPotentialSales', $dashboard->getFullPotentialSales());
        $this->setData('couponsAffiliate', $dashboard->getCouponsAffiliate());
        $this->setData('affiliateCredit', $dashboard->getCreditAffiliatePlus());
        $this->setData('productStockItem', $dashboard->getProductStockItem());

        $intervalDays = (int)isset($_POST['submit']) ? $_POST['interval'] : 0;

        switch ($intervalDays) {
            case 1:
                $this->setData('fullSalesPrice', $dashboard->getFullSalesPrice($dashboard::INTERVAL_TODAY));
                $this->setData('fullProfits', $dashboard->getFullProfits($dashboard::INTERVAL_TODAY));
                $this->setData('productMoreSold', $dashboard->getProductMoreSold($dashboard::INTERVAL_TODAY));
                $this->setData('productMoreProfit', $dashboard->getProductMoreProfit($dashboard::INTERVAL_TODAY));
                $this->setData('fullPurchases', $dashboard->getFullPurchases($dashboard::INTERVAL_TODAY));
                $this->setData('generateMoreCommision', $dashboard->getGenerateMoreCommission($dashboard::INTERVAL_TODAY));

                $arrComboBoxSelected = array(
                        0 => '',
                        1 => 'selected="selected"',
                        7 => '',
                        30 => '',
                );

                $this->setData('comboBoxSelected', $arrComboBoxSelected);

                break;

            case 7:
                $this->setData('fullSalesPrice', $dashboard->getFullSalesPrice($dashboard::INTERVAL_SEVEN_DAYS));
                $this->setData('fullProfits', $dashboard->getFullProfits($dashboard::INTERVAL_SEVEN_DAYS));
                $this->setData('productMoreSold', $dashboard->getProductMoreSold($dashboard::INTERVAL_SEVEN_DAYS));
                $this->setData('productMoreProfit', $dashboard->getProductMoreProfit($dashboard::INTERVAL_SEVEN_DAYS));
                $this->setData('fullPurchases', $dashboard->getFullPurchases($dashboard::INTERVAL_SEVEN_DAYS));
                $this->setData('generateMoreCommision', $dashboard->getGenerateMoreCommission($dashboard::INTERVAL_SEVEN_DAYS));

                $arrComboBoxSelected = array(
                        0 => '',
                        1 => '',
                        7 => 'selected="selected"',
                        30 => '',
                );

                $this->setData('comboBoxSelected', $arrComboBoxSelected);

                break;

            case 30:
                $this->setData('fullSalesPrice', $dashboard->getFullSalesPrice($dashboard::INTERVAL_THIRTY_DAYS));
                $this->setData('fullProfits', $dashboard->getFullProfits($dashboard::INTERVAL_THIRTY_DAYS));
                $this->setData('productMoreSold', $dashboard->getProductMoreSold($dashboard::INTERVAL_THIRTY_DAYS));
                $this->setData('productMoreProfit', $dashboard->getProductMoreProfit($dashboard::INTERVAL_THIRTY_DAYS));
                $this->setData('fullPurchases', $dashboard->getFullPurchases($dashboard::INTERVAL_THIRTY_DAYS));
                $this->setData('generateMoreCommision', $dashboard->getGenerateMoreCommission($dashboard::INTERVAL_THIRTY_DAYS));

                $arrComboBoxSelected = array(
                        0 => '',
                        1 => '',
                        7 => '',
                        30 => 'selected="selected"',
                );

                $this->setData('comboBoxSelected', $arrComboBoxSelected);
                break;

            default:
                $this->setData('fullSalesPrice', $dashboard->getFullSalesPrice());
                $this->setData('fullProfits', $dashboard->getFullProfits());
                $this->setData('productMoreSold', $dashboard->getProductMoreSold());
                $this->setData('productMoreProfit', $dashboard->getProductMoreProfit());
                $this->setData('fullPurchases', $dashboard->getFullPurchases());
                $this->setData('generateMoreCommision', $dashboard->getGenerateMoreCommission());

                $arrComboBoxSelected = array(
                        0 => 'selected="selected"',
                        1 => '',
                        7 => '',
                        30 => '',
                );

                $this->setData('comboBoxSelected', $arrComboBoxSelected);
                break;
        }
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }
}