<?php

class Super_Awesome_Adminhtml_Report_ExampleController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {

        $this->loadLayout()
                ->_addBreadcrumb(Mage::helper('awesome')->__('Awesome'), Mage::helper('awesome')->__('Awesome'));
        return $this;
    }

    public function simpleAction()
    {
        $this->_title($this->__('Awesome'))->_title($this->__('Reports'))->_title($this->__('Simple Report'));

        $this->_initAction()
                ->_setActiveMenu('awesome/report')
                ->_addBreadcrumb(Mage::helper('awesome')->__('Simple Example Report'), Mage::helper('awesome')->__('Simple Example Report'))
                ->_addContent($this->getLayout()->createBlock('awesome/adminhtml_report_simple'))
                ->renderLayout();

    }

    public function exportSimpleCsvAction()
    {
        $fileName   = 'simple.csv';
        $content    = $this->getLayout()->createBlock('awesome/adminhtml_report_simple_grid')
                      ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}