<?php
class MarioSam_RepublicaVirtual_AjaxController extends Mage_Core_Controller_Front_Action
{
	public function enderecoAction()
	{
		//$client = new Zend_Http_Client('http://republicavirtual.com.br/web_cep.php?cep='.urlencode($this->getRequest()->getParam('cep', false)));
		$client = new Zend_Http_Client('http://webservice.kinghost.net/web_cep.php?auth=040518de34397ca6687327a975fdbd56&formato=xml&cep='.urlencode($this->getRequest()->getParam('cep', false)));

		$res = $client->request();
		$xml = simplexml_load_string($res->getBody());
		$xml->uf_id = $this->getUFID((string) $xml->uf);
		echo json_encode($xml);

		// {"resultado":"0","resultado_txt":"sucesso - cep n\u00e3o encontrado","uf":{},"cidade":{},"bairro":{},"tipo_logradouro":{},"logradouro":{},"uf_id":{}}
	}

	public function enderecoAction0()
	{
		require('phpQuery.php');

		$html = $this->simple_curl('http://m.correios.com.br/movel/buscaCepConfirma.do',array(
			'cepEntrada'	=> $this->getRequest()->getParam('cep', false),
			'tipoCep'		=> '',
			'cepTemp'		=> '',
			'metodo'		=> 'buscarCep'
		));

		phpQuery::newDocumentHTML($html, $charset = 'utf-8');


		$erro = trim(pq('.conteudo .erro:eq(0)')->html());

		$logradouro 		= '';
		$tipo_logradouro	= '';

		if (!$erro) {
			$resultado 		= "1";
			$resultado_txt	= "sucesso - cep completo";

			$aux_logradouro 	= trim(pq('.caixacampobranco .resposta:contains("Logradouro: ") + .respostadestaque:eq(0)')->html());
			$arr_logradouro 	= explode(' ', $aux_logradouro);
			$tot_logradouro		= count($arr_logradouro);
			$tipo_logradouro	= $arr_logradouro[0];

			if (!strpos($aux_logradouro,'/')) {
				for ($x=1; $x < $tot_logradouro; $x++){
					$logradouro .= $arr_logradouro[$x]." ";
				}
				$logradouro = substr($logradouro,0,-1);
			} else {
				$resultado 			= "2";
				$tipo_logradouro	= "";
				$logradouro 		= "";
			}
		} else {
			$resultado 		= "0";
			$resultado_txt	= "sucesso - cep n&atilde;o encontrado";
		}

		$dados =
				array(
					'resultado'			=> $resultado,
					'resultado_txt'		=> $resultado_txt,
					'tipo_logradouro'	=> $tipo_logradouro,
					'logradouro'		=> $logradouro,
					'bairro'			=> trim(pq('.caixacampobranco .resposta:contains("Bairro: ") + .respostadestaque:eq(0)')->html()),
					'cidade/uf'			=> trim(pq('.caixacampobranco .resposta:contains("Localidade / UF: ") + .respostadestaque:eq(0)')->html()),
					'cep'				=> trim(pq('.caixacampobranco .resposta:contains("CEP: ") + .respostadestaque:eq(0)')->html())
				);

		$dados['cidade/uf'] = explode('/',$dados['cidade/uf']);
		$dados['cidade'] 	= trim($dados['cidade/uf'][0]);
		$dados['uf'] 		= trim($dados['cidade/uf'][1]);
		$dados['uf_id'] 	= (string) $this->getUFID(trim($dados['cidade/uf'][1]));
		unset($dados['cidade/uf']);

		echo json_encode($dados);

	}//enderecoAction

	private function simple_curl($url,$post=array(),$get=array()){
		$url = explode('?',$url,2);
		if(count($url)===2){
			$temp_get = array();
			parse_str($url[1],$temp_get);
			$get = array_merge($get,$temp_get);
		}

		$ch = curl_init($url[0]."?".http_build_query($get));
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		return curl_exec ($ch);
	}

	private function getUFID($str)
	{
		$arr = array();
		$arr['']   = '';
		$arr['AC'] = 485;
		$arr['AL'] = 486;
		$arr['AP'] = 487;
		$arr['AM'] = 488;
		$arr['BA'] = 489;
		$arr['CE'] = 490;
		$arr['DF'] = 511;
		$arr['ES'] = 491;
		$arr['GO'] = 492;
		$arr['MA'] = 493;
		$arr['MT'] = 494;
		$arr['MS'] = 495;
		$arr['MG'] = 496;
		$arr['PA'] = 497;
		$arr['PB'] = 498;
		$arr['PR'] = 499;
		$arr['PE'] = 500;
		$arr['PI'] = 501;
		$arr['RJ'] = 502;
		$arr['RN'] = 503;
		$arr['RS'] = 504;
		$arr['RO'] = 505;
		$arr['RR'] = 506;
		$arr['SC'] = 507;
		$arr['SP'] = 508;
		$arr['SE'] = 509;
		$arr['TO'] = 510;

		return $arr[$str];
	}//getUFID

}//MarioSam_RepublicaVirtual_AjaxController