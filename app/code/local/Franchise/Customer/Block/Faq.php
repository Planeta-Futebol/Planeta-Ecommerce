<?php

class Franchise_Customer_Block_Faq extends Inic_Faq_Block_Frontend_List
{
    public function __construct()
    {
        parent::__construct();
    }


    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }
}