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
class Devinc_Ajaxcart_Block_Adminhtml_System_Config_Form_Dragdrop extends Devinc_License_Block_Adminhtml_System_Config_Form_Text
{    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$script = "<script type=\"text/javascript\">	
                        var value = $('ajaxcart_dragdrop_enable_category_dragdrop').value;
                        if (value==1 || value==3) {
                            $('row_ajaxcart_dragdrop_floater_text_color').show();
                            $('row_ajaxcart_dragdrop_floater_bkg').show();
                        } else {
                            $('row_ajaxcart_dragdrop_floater_text_color').hide();
                            $('row_ajaxcart_dragdrop_floater_bkg').hide();
                        }

                        $('ajaxcart_dragdrop_enable_category_dragdrop').observe('change', function(event) {
                            var value = $('ajaxcart_dragdrop_enable_category_dragdrop').value;
                            if (value==1 || value==3) {
                                $('row_ajaxcart_dragdrop_floater_text_color').show();
                                $('row_ajaxcart_dragdrop_floater_bkg').show();
                            } else {
                                $('row_ajaxcart_dragdrop_floater_text_color').hide();
                                $('row_ajaxcart_dragdrop_floater_bkg').hide();
                            }
                        });
	    			</script>";
    	
        return $element->getElementHtml().$script;
    }  
}
 