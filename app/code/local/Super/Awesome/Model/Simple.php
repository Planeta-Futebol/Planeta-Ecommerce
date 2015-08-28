<?php
class Super_Awesome_Model_Simple extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('awesome/simple');
    }
}