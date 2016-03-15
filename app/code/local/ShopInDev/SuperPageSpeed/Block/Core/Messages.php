<?php
/**
 * ShopInDev
 *
 * @category    ShopInDev
 * @package     ShopInDev_SuperPageSpeed
 * @copyright   Copyright (c) 2014 ShopInDev
 * @license     http://opensource.org/licenses/GPL-3.0 GNU General Public License (GPL)
 */

class ShopInDev_SuperPageSpeed_Block_Core_Messages extends Mage_Core_Block_Messages {

    /**
     * Storage for used types of message storages
     * @var array
     */
    protected $_usedStorageTypes = array('core/session');

    /**
     * Add used storage type
     * @param string $type
     */
    public function addStorageType($type){
        $this->_usedStorageTypes[] = $type;
    }

    /**
     * Retrieve messages in HTML format grouped by type
     * @return  string
     */
    public function getGroupedHtml(){

        $html = parent::getGroupedHtml();

        // Use single transport object instance for all message blocks
        // This will fix block messages do allow hole punching
        $_transportObject = new Varien_Object;
        $_transportObject->setHtml($html);

        Mage::dispatchEvent('core_block_messages_to_grouped_html_after',
            array('block' => $this, 'transport' => $_transportObject));

        $html = $_transportObject->getHtml();

        return $html;
    }

}