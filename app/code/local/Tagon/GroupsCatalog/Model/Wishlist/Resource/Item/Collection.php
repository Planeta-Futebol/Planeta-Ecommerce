<?php


class Tagon_GroupsCatalog_Model_Wishlist_Resource_Item_Collection
    extends Mage_Wishlist_Model_Resource_Item_Collection
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'wishlist_item_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'collection';
}
