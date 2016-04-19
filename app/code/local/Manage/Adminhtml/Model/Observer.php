<?php

class Manage_Adminhtml_Model_Observer
{
    public function getCustomerGrid( $observer )
    {
        $block = $observer->getEvent()->getBlock();

        if ($block->getId() == 'customerGrid') {

            $block->removeColumn('taxvat');
            $block->removeColumn('gender');
            $block->removeColumn('dob');
            $block->removeColumn('age');

            $block->addColumn( 'account_name', array(
                    'header'    => Mage::helper('customer')->__('Nome do Representante'),
                    'index'     => 'account_name',
                )
            );
        }
    }

    public function beforeCollectionLoad( $observer )
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }

        /**
         * Mage_Customer_Model_Resource_Customer_Collection
         */
        if ($collection instanceof Mage_Customer_Model_Resource_Customer_Collection) {
            /* @var $collection Mage_Customer_Model_Resource_Customer_Collection */
            $collection->getSelect()
                ->joinLeft(
                    [ 'aft' => 'affiliateplus_transaction'],
                    'aft.customer_id = e.entity_id',
                    ['account_name']
                )->group('e.entity_id');
        }
    }
}