<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
set_time_limit(7200);
 
require 'app/Mage.php';

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please complete install wizard first.";
    exit;
}
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);


// Only for urls
// Don't remove this
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', $_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

$objNf = Mage::getModel('fuza_bling/blingnf')->getCollection()->addFieldToFilter('main_table.status', 'processing')->addFieldToFilter('main_table.status_gateway', '6');

if($objNf->count() > 0 ){

	$apikey = Mage::getStoreConfig('fuza_bling/geral/apikey');

	foreach($objNf as $_nf){

		$documentNumber = $_nf->getId();

		//echo 'documentNumber: '.$documentNumber.'<br>';

		$documentSerie = 1;
		//$outputType = "json";
		$outputType = "xml";
		$url = 'https://bling.com.br/Api/v2/notafiscal/' . $documentNumber . '/'. $documentSerie . '/' . $outputType;

		/*
		$retorno = executeGetFiscalDocument($url, $apikey);
		echo $retorno;
		*/

		$retorno = new Varien_Simplexml_Config(executeGetFiscalDocument($url, $apikey) );

		$erros = $retorno->getNode("erros");

		if (!$erros) {

			$nota = $retorno->getNode("notasfiscais");
			$nota = $nota->asArray();
			$nota = $nota['notafiscal'];

			//print_r($nota);

			/*
			echo $nota['situacao'];
			echo $nota['chaveacesso'];
			echo $nota['xml'];
			*/

			if ($nota['situacao'] != 'Rejeitada' && $nota['situacao'] != 'Pendente') {
				echo "NFe: ".$documentNumber.", status: ".$nota['situacao']."<br>";

				$_nf->setStatusGateway(2);
				$_nf->setNfKey($nota['chaveacesso']);
				$_nf->setNfDanfe($nota['xml']);
				//$_nf->setStatusMessage('Nota Fiscal Enviada - Autorizado o uso da NF-e');
				$_nf->setStatusMessage($nota['situacao']);
				$_nf->setErrorMessage("");
				$_nf->setStatus('finished');
				$_nf->setUpdatedAt(Mage::getModel('core/date')->date());
				$_nf->save();
			}
		}

//break;
	}
}


function executeGetFiscalDocument($url, $apikey){
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url . '&apikey=' . $apikey);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);

    $response = curl_exec($curl_handle);
        
    curl_close($curl_handle);
 
    return $response;
}
