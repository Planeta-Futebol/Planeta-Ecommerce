<?php
/**
 * Luciano Fuza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the New BSD License.
 * It is also available through the world-wide-web at this URL:
 * http://www.pteixeira.com.br/new-bsd-license/
 *
 * @category   Fuza
 * @package    Fuza_Etiqueta
 * @copyright  Copyright (c) 2013 Luciano Fuza (http://www.fuza.com.br)
 * @author     Luciano Fuza <luciano@fuza.com.br>
 * @license    http://www.fuza.com.br/new-bsd-license/ New BSD License
 */

class Fuza_Bling_Model_Source_Status
{
	public function toOptionArray()
	{
		return array(
		  array('value' => 'created', 'label' => Mage::helper('adminhtml')->__('Reservada')),
		  array('value' => 'processing', 'label' => Mage::helper('adminhtml')->__('Processando')),
		  array('value' => 'not_used', 'label' => Mage::helper('adminhtml')->__('Não Usada')),
		  array('value' => 'finished', 'label' => Mage::helper('adminhtml')->__('Integrada')),
		  array('value' => 'spare', 'label' => Mage::helper('adminhtml')->__('Avulsa')),
		);
	}
	
   public function toColumnOptionArray()
    {
    	return array(
			'created' => Mage::helper('adminhtml')->__('Reservada'),
			'processing' => Mage::helper('adminhtml')->__('Processando'),
			'not_used' => Mage::helper('adminhtml')->__('Não Usada'),
			'finished' => Mage::helper('adminhtml')->__('Integrada'),
			'spare' => Mage::helper('adminhtml')->__('Avulsa'),
    	);
    } 
}