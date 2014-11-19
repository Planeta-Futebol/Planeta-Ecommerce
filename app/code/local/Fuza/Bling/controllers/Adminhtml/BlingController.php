<?php

class Fuza_Bling_Adminhtml_BlingController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{

        $this->_title('Bling NFe');
        $this->loadLayout();
        $this->_setActiveMenu('fuza_bling');
        $this->_addContent($this->getLayout()->createBlock('fuza_bling/adminhtml_bling'));
        $this->renderLayout();
	}

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('fuza_bling/adminhtml_bling_grid')->toHtml()
        );
    }

    public function exportCsvAction()
    {
        $fileName   = 'bling.csv';
        $content    = $this->getLayout()->createBlock('bling/adminhtml_bling_grid')->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'bling.xml';
        $content    = $this->getLayout()->createBlock('bling/adminhtml_bling_grid')->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
	public function alocaNfAvulsaAction()
	{
	
		if (!Mage::getStoreConfig('fuza_bling/geral/enable_bling')) {
			$this->_getSession()->addError('Módulo Fuza Bling está desabilitado.');
			Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("bling/adminhtml_bling"));
		} else {
			try {
				// inicia processo de geração de NFe
				$objNf = Mage::getModel('fuza_bling/blingnf');
				$objNf->setOrderId(0);
				$objNf->setStatus('spare');
				$objNf->setStatusMessage('Número de NFe alocado para uso avulso.');
				$objNf->setCreatedAt(Mage::getModel('core/date')->date());
				$objNf->setUpdatedAt(Mage::getModel('core/date')->date());
				$objNf->save();

				$this->_getSession()->addSuccess('NFe #'.$objNf->getId().' alocada, anote este número para utilizar diretamente no Bling.');

			} catch (PDOException $e) {
				$this->_getSession()->addError('Não foi possível alocar um número de NFe. Motivo: '.$e->getMessage());
			}
		}
		
		Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("bling/adminhtml_bling"));
    }

}
