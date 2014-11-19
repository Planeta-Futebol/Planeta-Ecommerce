<?php
class Fuza_Bling_Model_Api_Bling {

  public function send($args = array()) {

    try {
      $data = "apiKey=".$args['apiKey']."&pedidoXML=".$args['pedidoXML'];

      //$response = $this->enviarPedido('http://www.bling.com.br/recepcao.nfe.php', $data);
      $response = $this->enviarPedido(Mage::getStoreConfig('fuza_bling/geral/url_recebe_pedido'), $data);

    } catch (Exception $e) {
      $response = $e->getMessage();
      Mage::log("Erro enviando pedidos: ",null,'Fuza_Bling_NFe.log');
      Mage::log($response,null,'Fuza_Bling_NFe.log');
    }

    return $response;
  }

  function gera($args = array()){

    $data = "apiKey=".$args['apiKey']."&numero=".$args['numNF']."&serie=".$args['numSerie']."&enviaEmailAoCliente=".$args['enviaEmail'];

    try {
      //$response = $this->enviarPedido('http://www.bling.com.br/recepcao.nfe.emissao.php', $data);
      $response = $this->enviarPedido(Mage::getStoreConfig('fuza_bling/geral/url_gera_pedido'), $data);

    } catch (Exception $e) {
      $response = false;
      /*
      echo $e->getMessage();
      echo '<br>exception';
      */
    }

    return $response;
  }
  function enviarPedido($url, $data, $optional_headers = null) {
    $params = array('http' => array(
          'method' => 'POST',
          'content' => $data
         ));
    if ($optional_headers !== null) {
      $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp) {
      throw new Exception("Problema com $url, $php_errormsg");
      Mage::log("Problema com $url, $php_errormsg",null,'Fuza_Bling_NFe.log');
    }
    $response = @stream_get_contents($fp);
    if ($response === false) {
      throw new Exception("Problema obtendo retorno de $url, $php_errormsg");
      Mage::log("Problema obtendo retorno de $url, $php_errormsg",null,'Fuza_Bling_NFe.log');
    }
    return $response;
  }

}