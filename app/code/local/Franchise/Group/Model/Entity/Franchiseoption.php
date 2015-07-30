<?php

class Franchise_Group_Model_Entity_Franchiseoption extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();
            $this->_options[] = array(
                    'value' => 1,
                    'label' => 'Cliente Varejo'
            );
            $this->_options[] = array(
                    'value' => 2,
                    'label' => 'Loja Móvel'
            );
            $this->_options[] = array(
                    'value' => 3,
                    'label' => 'Stand Lojista'
            );
            $this->_options[] = array(
                    'value' => 4,
                    'label' => 'Quiosque de Shopping'
            );

        }

        return $this->_options;
    }

}