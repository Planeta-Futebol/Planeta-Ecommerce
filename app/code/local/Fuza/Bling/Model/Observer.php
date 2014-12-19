<?php

class Fuza_Bling_Model_Observer{

	public function alocaNf($observer)
	{
		if (!Mage::getStoreConfig('fuza_bling/geral/enable_bling'))
			return $this;

		/* from shipment
		$event 		= $observer->getEvent();
		$shipment 	= $event->getShipment();
		$order 		= $shipment->getOrder();
		$id 		= $order->getData('entity_id');
		*/


		/* from invoice
		*/
		$event 		= $observer->getEvent();
		$invoice 	= $event->getInvoice();
		$order 		= $invoice->getOrder();
		$id 		= $order->getId();

		$order = Mage::getModel('sales/order')->load($id);

		if(!$order->getId()){
			return $this;
		}


//		if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE || $order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING){
//		if($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE || $order->getStatus() == Mage_Sales_Model_Order::STATE_PROCESSING){
//		if($order->getState() == "new" && $order->getStatus() == "complete"){

/*
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

			$group = 'F';
			if($customer->getId()){
				if ($customer->getGroupId() == 2)
					$group = 'J';
			}
*/

			// regra por 3 Custom
			//if ( (($id % 3) == 1) || ($order->getShippingAddress()->getRegion() == 'Amazonas') || $group == "J"){

				if ($order->getGrand_total() > 0) {

					// verificar se já nao existe para não criar novamente
					$blingNF = Mage::getModel('fuza_bling/blingnf')->load( $order->getId(), 'order_id' );
					$nf = $blingNF->getData();

					// inicia processo de geração de NFe
					if (!$nf):
						$objNf = Mage::getModel('fuza_bling/blingnf');
						$objNf->setOrderId($id);
						$objNf->setStatus('created');
						$objNf->setCreatedAt(Mage::getModel('core/date')->date());
						$objNf->save();
/*
						if ($order->getShippingAddress()->getRegion() == 'Amazonas'):
							$comment = '#ATENÇÃO: PEDIDO DEVE TER NOTA FISCAL IMPRESSA E ANEXADA NO LADO EXTERNO DA EMBALAGEM';
							$order->addStatusHistoryComment($comment, false)->setIsCustomerNotified(false);
							$order->save();
						endif;
*/
					endif;
				}

			//}  // regra por 3 Custom
//		}

		return $this;
	}

	public function sendNf(){

		if (!Mage::getStoreConfig('fuza_bling/geral/enable_bling') || !Mage::getStoreConfig('fuza_bling/geral/apikey'))
			return $this;


		$lista_grupos = Mage::getModel('customer/group')->getCollection();
		$grupos = array();
		foreach ($lista_grupos as $grupo)
		  $grupos[$grupo->getCustomerGroupId()] = $grupo->getCustomerGroupCode();


		$apikey = Mage::getStoreConfig('fuza_bling/geral/apikey');


		header("content-type:text/html;charset=utf-8");

		$objNf = Mage::getModel('fuza_bling/blingnf')->getCollection()->addFieldToFilter('main_table.status', 'created');

		if($objNf->count() > 0 ){

			foreach($objNf as $_nf){

				$order = Mage::getModel('sales/order')->load($_nf->getOrderId());

				// assegurar que pedido com total zero não será enviado.
				if ($order->getGrand_total() > 0) {

					$customer_address = $order->getBillingAddress();

					$payment = $order->getPayment();

					$street = $customer_address->getStreet();

					if (strlen(trim($street[3])) == 0)
						$bairro = "-";
					else
						$bairro = $street[3];

					$content = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
					$content .= '<pedido>'.PHP_EOL;
					$content .= '<numero_nf>'.$_nf->getId().'</numero_nf>'.PHP_EOL;

					$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

					if($customer->getTipopessoa() == "Pessoa Física") {
						$group = 'F';
					} else {
						$group = 'J';
					}

					if ($customer_address->getRegioncode() == "SP") {
						$nat_operacao = "Venda para SP";
					} else {
						if ($group == "F")
							$nat_operacao = "Venda de Mercadorias para Fora de SP - Isento";
						else {
							if (strlen(trim($customer->getIe())) > 0) {
								$nortes = array("AC", "AP", "AM", "PA", "RO", "RR", "TO", "AL", "BA", "CE", "MA", "PB", "PE", "PI", "RN", "SE", "ES");
								if (in_array($customer_address->getRegioncode(), $nortes))
									$nat_operacao = "Venda de Mercadoria Norte e Nordeste e ES - Contribuinte";
								else
									$nat_operacao = "Venda de Mercadorias para Sul e Sudeste  Menos ES - Contribuinte";
							} else
								$nat_operacao = "Venda de Mercadorias para Fora de SP - Isento";
						}
					}

					$content .= '<nat_operacao>'.$nat_operacao.'</nat_operacao>'.PHP_EOL;

					//CLIENTE
					$content .= '<cliente>'.PHP_EOL;
						$content .= '<nome>'.$customer_address->getName().'</nome>'.PHP_EOL;
						$content .= '<tipoPessoa>'.$group.'</tipoPessoa>'.PHP_EOL;

						$content .= '<cpf_cnpj>'.$customer->getTaxvat().'</cpf_cnpj>'.PHP_EOL;

						if ($group == "F") {
							$content .= '<ie_rg>'.$customer->getRg().'</ie_rg>';
						} else {
							if (strlen(trim($customer->getIe())) > 0)
								$content .= '<ie_rg>'.$customer->getIe().'</ie_rg>';
							else
								$content .= '<ie_rg>ISENTO</ie_rg>';
						}

						$content .= '<endereco>'.$street[0].'</endereco>'.PHP_EOL;
						$content .= '<numero>'.$street[1].'</numero>'.PHP_EOL;
//						$content .= '<complemento>'.$street[2].'</complemento>'.PHP_EOL;
						$content .= '<complemento>'.strtr(strip_tags($street[2]), "&", "e").'</complemento>'.PHP_EOL;
						$content .= '<bairro>'.$bairro.'</bairro>'.PHP_EOL;
						$content .= '<cep>'.$customer_address->getPostcode().'</cep>'.PHP_EOL;
						$content .= '<cidade>'.$customer_address->getCity().'</cidade>'.PHP_EOL;
						$content .= '<uf>'.$customer_address->getRegioncode().'</uf>'.PHP_EOL;
						$content .= '<fone>'.$customer_address->getTelephone().'</fone>'.PHP_EOL;
						$content .= '<email>'.$customer_address->getEmail().'</email>'.PHP_EOL;
					$content .= '</cliente>'.PHP_EOL;

					//ITENS
					$content .= '<itens>'.PHP_EOL;

					$_itens = $order->getAllItems();

					foreach($_itens as $_item)
					{
						if ($_item->getParentItemId())
							continue;

						$content .= '<item>'.PHP_EOL;
							//$content .= '<codigo>'.$_item->getProductId().'</codigo>'.PHP_EOL;
							$content .= '<codigo>'.$_item->getSku().'</codigo>'.PHP_EOL;
							//$content .= '<descricao>'.$_item->getName().'</descricao>'.PHP_EOL;
							$content .= '<descricao>'.strtr(strip_tags($_item->getName()), "&", "e").'</descricao>'.PHP_EOL;
							$content .= '<un>Un</un>'.PHP_EOL; /* checar */
							$content .= '<qtde>'.(int)$_item->getQtyOrdered().'</qtde>'.PHP_EOL;
							$content .= '<vlr_unit>'.$_item->getPrice().'</vlr_unit>'.PHP_EOL;
							$content .= '<tipo>P</tipo>'.PHP_EOL; /* checar */
							$content .= '<peso_bruto>'.$_item->getWeight().'</peso_bruto>'.PHP_EOL;
							$content .= '<peso_liq>'.$_item->getWeight().'</peso_liq>'.PHP_EOL;
							$content .= '<class_fiscal>'.$_item->getProduct()->getNcm().'</class_fiscal>'.PHP_EOL; /* checar */
							$content .= '<origem>0</origem>'.PHP_EOL; /* checar 0 = Nacional, 1 = Importado, 2 = Importado adquirido no mercado interno */
						$content .= '</item>'.PHP_EOL;
					}
					$content .= '</itens>'.PHP_EOL;

					//PARCELAS
					$content .= '<parcelas>'.PHP_EOL;
	/*
					$parcela = $payment->getAdditionalData();
					if($parcela){
						$pos = strpos($parcela, 'A');
						$n_parcela = substr($parcela, ($pos + 1));
						if( (int) $n_parcela > 1 ){
							$vlr_parc = number_format( $order->getGrandTotal()/$n_parcela, 2, '.', '' );
							$diff = 0;
							$subtotal = $vlr_parc * $n_parcela;
							if( number_format($subtotal, 2, '.', '' )  != number_format( $order->getGrandTotal(), 2, '.', '' ) ){
								$diff = number_format($order->getGrandTotal(), 2, '.', '' ) - number_format($subtotal, 2, '.', '' );
							}

							for($i =0; $i< $n_parcela; $i ++){
								$content .= '<parcela>'.PHP_EOL;
									$content .= '<dias>30</dias>'.PHP_EOL;
									$content .= '<data>'.date('d/m/Y', strtotime($order->getCreatedAt())).'</data>'.PHP_EOL;
									if($i == $n_parcela - 1){
										$content .= '<vlr>'.($vlr_parc + $diff).'</vlr>'.PHP_EOL;
									}else{
										$content .= '<vlr>'.$vlr_parc.'</vlr>'.PHP_EOL;
									}
									$content .= '<obs>-</obs>'.PHP_EOL;
								$content .= '</parcela>'.PHP_EOL;
							}
						} else {
							$content .= '<parcela>'.PHP_EOL;
								$content .= '<dias>1</dias>'.PHP_EOL;
								$content .= '<data>'.date('d/m/Y', strtotime($order->getCreatedAt())).'</data>'.PHP_EOL;
								$content .= '<vlr>'.number_format( $order->getGrandTotal(), 2, '.', '' ).'</vlr>'.PHP_EOL;
								$content .= '<obs>-</obs>'.PHP_EOL;
							$content .= '</parcela>'.PHP_EOL;
						}
					}else{
	*/
						if (date('d/m/Y', strtotime($order->getCreatedAt())) == date('d/m/Y')) {
							$content .= '<parcela>'.PHP_EOL;
								$content .= '<dias>1</dias>'.PHP_EOL;
								$content .= '<data>'.date('d/m/Y', strtotime($order->getCreatedAt())).'</data>'.PHP_EOL;
								$content .= '<vlr>'.number_format( $order->getGrandTotal(), 2, '.', '' ).'</vlr>'.PHP_EOL;
								$content .= '<obs>-</obs>'.PHP_EOL;/* checar */
							$content .= '</parcela>'.PHP_EOL;
						}
	//				}
					$content .= '</parcelas>'.PHP_EOL;

					//TOTAIS
					$content .= '<vlr_frete>'.number_format( $order->getShippingAmount(), 2, '.', '' ).'</vlr_frete>'.PHP_EOL;
					$content .= '<vlr_seguro>0</vlr_seguro>'.PHP_EOL;
					$content .= '<vlr_despesas>0</vlr_despesas>'.PHP_EOL;

					$vlr_desconto = $order->getDiscountAmount();
					if ($vlr_desconto < 0)
						$vlr_desconto = $vlr_desconto * (-1);

					$content .= '<vlr_desconto>'.number_format( $vlr_desconto, 2, '.', '' ).'</vlr_desconto>'.PHP_EOL;

					$content .= '<obs></obs>'.PHP_EOL;
	//				$content .= '<obs_internas></obs_internas>'.PHP_EOL;
					$content .= '</pedido>'.PHP_EOL;

					$arrParam['apiKey'] 	= $apikey;
					$arrParam['pedidoXML'] 	= $content;

					$result = Mage::getModel('fuza_bling/api_bling')->send($arrParam);

					/*
					Possíveis situações de retorno
					OK
					Erro: Mensagem de erro
					*/

					if($result == "OK"){
						$_nf->setStatus('processing');
						$_nf->setErrorMessage('');
						$_nf->setUpdatedAt(Mage::getModel('core/date')->date());
						$_nf->save();
					} else {
						$_nf->setErrorMessage($result);
						$_nf->setUpdatedAt(Mage::getModel('core/date')->date());
						$_nf->save();
					}

				}
			}
		}

		return $this;
	}


	public function geraNf()
	{

		if (!Mage::getStoreConfig('fuza_bling/geral/enable_bling') || !Mage::getStoreConfig('fuza_bling/geral/apikey'))
			return $this;

		header("content-type:text/html;charset=utf-8");

		$apikey = Mage::getStoreConfig('fuza_bling/geral/apikey');

		$objNf = Mage::getModel('fuza_bling/blingnf')->getCollection()->addFieldToFilter('main_table.status', 'processing');

		if($objNf->count() > 0 )
		{

			foreach($objNf as $_nf)
			{

				$arrParam['apiKey'] 	= $apikey;
				$arrParam['numNF'] 		= $_nf->getId();
				$arrParam['numSerie'] 	= "1";
				$arrParam['enviaEmail'] = "S";

				$result = new Varien_Simplexml_Config(Mage::getModel('fuza_bling/api_bling')->gera($arrParam));

				if($result){

					/*
					Possíveis situações de retorno
					0 - Não enviada
					1 - Rejeitada
					2 - Autorizada
					3 - Aguardando protocolo ou recibo de entrega
					4 - Denegada
					5 - Exceção
					6 - Nota fiscal não localizada
					7 - Erros nos parâmetros enviados para a emissão da NFe
					*/

					if ($result->getNode('situacao') == 2) {
						$_nf->setNfDanfe($result->getNode('linkDanfe'));
						$_nf->setNfKey($result->getNode('chaveAcesso'));
						$_nf->setStatusGateway($result->getNode('situacao'));
						$_nf->setStatusMessage($result->getNode('mensagem'));
						$_nf->setErrorMessage("");
						$_nf->setStatus('finished');
						$_nf->setUpdatedAt(Mage::getModel('core/date')->date());
						$_nf->save();

						$order = Mage::getModel('sales/order')->load($_nf->getOrderId());

						// add historico do pedido
						$order->addStatusHistoryComment('NFe Gerada e enviada ao email do cliente', false)->setIsCustomerNotified(true);
						$order->save();

						// dispatch an event after status has changed
						//Mage::dispatchEvent('sales_order_status_after', array('order' => $this, 'state' => $state, 'status' => $status, 'comment' => $comment, 'isCustomerNotified' => $isCustomerNotified, 'shouldProtectState' => $shouldProtectState));

					} else {
						$aux_chaveAcesso = $result->getNode('chaveAcesso');
						if (isset($aux_chaveAcesso))
							$_nf->setNfKey($aux_chaveAcesso);

						$error_message = $result->getNode('erros');

						$_nf->setStatusGateway($result->getNode('situacao'));
						$_nf->setStatusMessage($result->getNode('mensagem'));
						$_nf->setErrorMessage($error_message);
						$_nf->setUpdatedAt(Mage::getModel('core/date')->date());
						$_nf->save();


						$order = Mage::getModel('sales/order')->load($_nf->getOrderId());

						$msg_aux_erro = "";

						$teste_erro = strpos($error_message, "vDesc");
						if ($teste_erro === false) {
						} else {
							$msg_aux_erro = "Valor do desconto é negativo: ".number_format( $order->getDiscountAmount(), 2, '.', '' );
						}

						$teste_erro = strpos($error_message, "cMun");
						if ($teste_erro === false) {
						} else {
							$msg_aux_erro = "Código do Município é inválido: ".$order->getBillingAddress()->getCity(). " / ".$order->getBillingAddress()->getRegioncode().", CEP: ".$order->getBillingAddress()->getPostcode();
						}

						$teste_erro = strpos($error_message, "NCM");
						if ($teste_erro === false) {
						} else {
							$msg_aux_erro = "NCM de algum produto é inválido.";
						}

						// Enviar email de erro

						$content[] = "NF <b>".$_nf->getId()."</b>, pedido <b>".$order->getIncrementId()."</b> possui um erro: ".$_nf->getStatusMessage();
						$content[] = "Mensagem do Bling: ".$error_message;

						if (strlen(trim($msg_aux_erro)) > 0)
							$content[] = "Mensagem Auxiliar: ".$msg_aux_erro;

						unset($error_message);
					}

				}
			}

			// Enviar email de erro
			if (count($content)>0)
			{
				$title = 'Erro ao gerar NFe no Bling';
				$content[] = "Verifique, corrija o(s) erro(s) e aguarde que a rotina automática de geração de NFe tente novamente ou execute a rotina GERA.";

				$this->enviaAvisos($title, $content);
			}
		}

		return $this;
	}


	public function enviaAvisos($title = null, $content = null)
	{
		if (!Mage::getStoreConfig('fuza_bling/geral/enable_bling'))
			return $this;

		if (!$title || !$content)
			return $this;

		// variáveis para dinamizar o conteúdo do template {{var nomeDaVariavel}} no arquivo.phtml
		$data = array();

		$email_aviso = Mage::getStoreConfig('fuza_bling/geral/email_aviso');
		$email_aviso = explode(",",$email_aviso);

		//$nomeloja = Mage::getStoreConfig('general/store_information/name');
		$nomeloja 	= Mage::getStoreConfig('trans_email/ident_general/name');
		$emailloja 	= Mage::getStoreConfig('trans_email/ident_general/email');

		$data['nomeloja']	= $nomeloja;
		$data['title'] 		= $title;
		$data['content'] 	= implode('<br />', $content);

		header("content-type:text/html;charset=utf-8");


		$storeId = Mage::app()->getStore()->getId();
		$templateId = 'bling_aviso';
		$mailSubject = '';
		$sender = array('name' => $nomeloja, 'email' => $emailloja);

		// carrega o objeto de envio de e-mails no template "superpedido_avisos" que acabamos de criar.
		$emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);

		foreach ($email_aviso as $email_aviso_unitario) {
			try {
				// Envia o e-mail com o template para SUPERPEDIDO >> CONFIGURAÇÕES >> GERAL
				$emailTemplate->setTemplateSubject($mailSubject)->sendTransactional($templateId, $sender, $email_aviso_unitario, $nomeloja, $data, $storeId);

				/* VALORES INICIAIS:
				Mage::getModel('core/config')->saveConfig('fuza_etiqueta/geral/ultimo_aviso', 'NUNCA ENVIADO.');
				*/

				Mage::getModel('core/config')->saveConfig('fuza_bling/geral/ultimo_aviso', date('d/m/Y \à\s H:i:s', Mage::getModel('core/date')->timestamp(time())));

			} catch (Exception $e) {

//				foreach ($email_aviso as $email_aviso_unitario) {
					$headers  = "MIME-Version: 1.0 \r\n";
					$headers .= "Content-type: text/html; charset=utf-8 \r\n";

					$headers .= "From: <$emailloja> \r\n";
					@mail($email_aviso_unitario, "Falha no envio do email <b>".$data['title']."</b> , VERIFIQUE!!", $data['content'].nl2br($e->getMessage()), $headers);
					//break;
//				}

			}
		}

		return $this;
	}

}

?>