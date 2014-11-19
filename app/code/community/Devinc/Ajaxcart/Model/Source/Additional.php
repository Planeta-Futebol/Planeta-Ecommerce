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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Devinc_Ajaxcart_Model_Source_Additional
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {        
        $options = array();
        $options[] = array('value' => '', 'label'=>Mage::helper('adminhtml')->__(''));
        $options[] = array('value' => array(
                                        array('value' => 'new', 'label'=>Mage::helper('adminhtml')->__('New Products')),
                                        array('value' => 'bestseller', 'label'=>Mage::helper('adminhtml')->__('Best Selling Products')),
                                        array('value' => 'mostviewed', 'label'=>Mage::helper('adminhtml')->__('Popular Products')),
                                        array('value' => 'upsell', 'label'=>Mage::helper('adminhtml')->__('Upsell Products')),
                                        array('value' => 'related', 'label'=>Mage::helper('adminhtml')->__('Related Products')),
                                        array('value' => 'crosssell', 'label'=>Mage::helper('adminhtml')->__('Cross-sell Products'))
                                    ), 
                            'label'=>Mage::helper('adminhtml')->__('Product Lists'));

        $staticBlocks = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('is_active', 1);
        $staticBlocksArray = array();
        if (count($staticBlocks)>0) {
            foreach ($staticBlocks as $block) {
                $staticBlocksArray[] = array('value' => $block->getIdentifier(), 'label'=>$block->getTitle());
            }
            $options[] = array('value' => $staticBlocksArray, 'label'=>Mage::helper('adminhtml')->__('Static Blocks'));
        }
        return $options;
    }

}
 