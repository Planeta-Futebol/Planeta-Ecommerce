<?php

class Fuza_Bling_Helper_Data extends Mage_Core_Helper_Abstract
{

    public static function explode($explode){
        return explode("_",$explode);
    }

    /*
     * Formata o telefone no formato aceito pelo I-PAGARE.
     */
    public function formatTelephone($telephone){
        $ddd = '';
        $tel = '';

        if(strlen($telephone) > 8){
            $telephone = str_ireplace("(", "", $telephone);
            $telephone = str_ireplace(")", "", $telephone);
            $telephone = str_ireplace("-", "", $telephone);
            $telephone = str_ireplace(" ", "", $telephone);
            if(strlen($telephone) == 9 ){
                $ddd = substr($telephone, 0,1);
                $tel = substr($telephone, 1);
            }else if(strlen($telephone) == 10 ){
                $ddd = substr($telephone, 0,2);
                $tel = substr($telephone, 2);
            }else if(strlen($telephone) >= 11 ){
                $ddd = substr($telephone, 0,3);
                $tel = substr($telephone, 3);
            }
        }else{
            $tel = $telephone;
        }

        return array($ddd,$tel);
    }

    public static function convertStringToDate($dataStatus, $horaStatus){
        $hora = "00";
        $minuto = "00";
        $segundo = "00";

        if($horaStatus != null && $horaStatus){
            $hora = substr($horaStatus,0,2);
            $minuto = substr($horaStatus,2,2);
            $segundo = substr($horaStatus,4,2);
        }

        $dia = substr($dataStatus,0,2);
        $mes = substr($dataStatus,2,2);
        $ano = substr($dataStatus,4);

        $data = strftime("%d/%m/%Y %X", mktime($hora,$minuto,$segundo,$mes,$dia,$ano));

        return $data;
    }

}