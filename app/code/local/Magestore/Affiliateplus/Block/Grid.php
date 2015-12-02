<?php
class Magestore_Affiliateplus_Block_Grid extends Mage_Core_Block_Template
{
	/**
	 * Columns of Grid
	 *
	 * @var array
	 * 
	 * Example of element:
	 * $_columns['id'] = array(
	 * 	'header'	=> 'ID',
	 * 	'align'		=> 'right',
	 * 	'width'		=> '50px',
	 * 	'index'		=> 'account_id',
	 * 	'type'		=> 'date' | 'options' | 'action' | 'datetime' | 'price' | 'baseprice'
	 *	'format'	=> 'medium',
	 * 	'options'	=> array( 'value' => 'label'),
	 * 	'action'	=> array(
	 * 					'label' => 'Edit',
	 * 					'url' 	=> 'affiliateplus/index/index',
	 * 					'name'	=> 'id',
	 * 					'field'	=> 'account_id',
	 * 					),
	 * 	'render'	=> 'function_name_of_parent_block(row)',
     *  'searchable'    => true | false,
     *  'filter_index'  => 'main_table.account_id'
     *  'filter_function'   => 'function_name_of_parent_block(collection, filterValues)'
	 * );
	 */
	protected $_columns = array();
	
	/**
	 * Grid's Collection
	 */
	protected $_collection;
	
	public function getColumns(){
		return $this->_columns;
	}

    /**
     * functions in mysql to join parts of
     * the name and return the full name.
     *
     * @var string
     */
    protected $fullNameConditionMysql = <<< 'CONCAT'
        CONCAT(IF(at_firstname.value IS NOT NULL AND at_firstname.value != '',
            CONCAT(LTRIM(RTRIM(at_firstname.value)), ' '),
            ''),
            IF(at_middlename.value IS NOT NULL AND at_middlename.value != '',
            CONCAT(LTRIM(RTRIM(at_middlename.value)), ' '),
            ''),
            LTRIM(RTRIM(at_lastname.value)))
CONCAT;
	
    /**
     * 
     * @param set collection and apply filter $collection
     * @return Magestore_Affiliateplus_Block_Grid
     */
	public function setCollection($collection){
		$this->_collection = $collection;
        if (!$this->getData('add_searchable_row')) {
            return $this;
        }
        foreach ($this->getColumns() as $columnId => $column) {
            if (isset($column['searchable']) && $column['searchable']) {
                if (isset($column['filter_function']) && $column['filter_function']) {
                    $this->fetchFilter($column['filter_function']);
                } else {
                    $field = isset($column['index']) ? $column['index'] : $columnId;
                    $field = isset($column['filter_index']) ? $column['filter_index'] : $field;
                    if ($filterValue = $this->getFilterValue($columnId)) {
                        $this->_collection->addFieldToFilter($field, array('like' => "%$filterValue%"));
                    }
                    if ($filterValue = $this->getFilterValue($columnId, '-from')) {
                        if ($column['type'] == 'price') {
                            $store = Mage::app()->getStore();
                            $filterValue /= $store->getBaseCurrency()->convert(1, $store->getCurrentCurrency());
                        } elseif ($column['type'] == 'date' || $column['type'] == 'datetime') {
                            $filterValue = date('Y-m-d', strtotime($filterValue));
                        }
                        $this->_collection->addFieldToFilter($field, array('gteq' => $filterValue));
                    }
                    if ($filterValue = $this->getFilterValue($columnId, '-to')) {
                        if ($column['type'] == 'price') {
                            $store = Mage::app()->getStore();
                            $filterValue /= $store->getBaseCurrency()->convert(1, $store->getCurrentCurrency());
                        } elseif ($column['type'] == 'date' || $column['type'] == 'datetime') {
                            $filterValue = date('Y-m-d', strtotime($filterValue)+86400);
                        }
                        $this->_collection->addFieldToFilter($field, array('lteq' => $filterValue));
                    }
                }
            }
        }

        $collection = $this->showByUserName($this->_collection);

        if($filterValue = $this->getFilterValue('name_user')){
            $this->findByNameUser($collection, $filterValue);
        }

        return $this;
	}
    
    public function getFilterValue($columnId = null, $offset = '') {
        if (!$this->hasData('filter_value')) {
            if ($filter = $this->getRequest()->getParam('filter')) {
                $filter = Mage::helper('core')->urlDecode($filter);
                parse_str($filter, $filter);
            }
            $this->setData('filter_value', $filter);
        }
        if (is_null($columnId)) {
            return $this->getData('filter_value');
        } else {
            return $this->getData('filter_value/' . $columnId . $offset);
        }
    }
    
    /**
     * fetch filter custom function
     * 
     * @param string $parentFuction
     * @return mixed
     */
    public function fetchFilter($parentFuction) {
        $parentBlock = $this->getParentBlock();
        return $parentBlock->$parentFuction($this->_collection, $this->getFilterValue());
    }
    
    public function getFilterUrl() {
        if (!$this->hasData('filter_url')) {
            $this->setData('filter_url', $this->getUrl('*/*/*'));
        }
        return $this->getData('filter_url');
    }
    
    public function getPagerHtml() {
        if ($this->getData('add_searchable_row')) {
            return $this->getParentBlock()->getPagerHtml();
        }
        return '';
    }
	
	public function getCollection(){
		return $this->_collection;
	}
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('affiliateplus/grid.phtml');
		return $this;
    }
    
    /**
     * Add new Column to Grid
     *
     * @param string $columnId
     * @param array $params
     * @return Magestore_Affiliateplus_Block_Grid
     */
    public function addColumn($columnId, $params){
        if (isset($params['searchable']) && $params['searchable']) {
            $this->setData('add_searchable_row', true);
            if (isset($params['type']) &&
                ($params['type'] == 'date' || $params['type'] == 'datetime')
            ) {
                $this->setData('add_calendar_js_to_grid', true);
            }
        }
    	$this->_columns[$columnId] = $params;
    	return $this;
    }
    
    /**
     * Call Render Function
     *
     * @param string $parentFunction
     * @param mixed $params
     * @return string
     */
    public function fetchRender($parentFunction, $row){
    	$parentBlock = $this->getParentBlock();
    	
    	$fetchObj = new Varien_Object(array(
    		'function'	=> $parentFunction,
    		'html'		=> false,
    	));
    	Mage::dispatchEvent("affiliateplus_grid_fetch_render_$parentFunction",array(
    		'block'	=> $parentBlock,
    		'row'	=> $row,
    		'fetch'	=> $fetchObj,
    	));
    	
    	if ($fetchObj->getHtml()) return $fetchObj->getHtml();
    	
    	return $parentBlock->$parentFunction($row);
    }


    /**
     * Retrieves the name in current collection, middle name, last
     * name of the users that are in the collection.
     *
     * @param $collection
     * @return $collection
     */
    private function showByUserName( $collection )
    {

        $select = $collection->getSelect();

        /**
         * Alias for the main tlable in collection.
         *
         * @var string $tableToJoin
         * */
        $tableToJoin = 'main_table';

        $tableCustomerVarchar = 'customer_entity_varchar';

        $firstNameTableAlias  = 'at_firstname';
        $middleNameTableAlias = 'at_middlename';
        $lastNameTableAlias   = 'at_lastname';

        /**
         * Join condition for retrieves first name user.
         *
         * @var array $firstNameJoinCondition
         * */
        $firstNameJoinCondition = array(
            "({$firstNameTableAlias}.entity_id = {$tableToJoin}.customer_id)
             AND ({$firstNameTableAlias}.attribute_id = 5)",
        );

        /**
         * Join condition for retrieves middle name user.
         *
         * @var array $middleNameJoinCondition
         * */
        $middleNameJoinCondition = array(
            "({$middleNameTableAlias}.entity_id = {$tableToJoin}.customer_id)
             AND ({$middleNameTableAlias}.attribute_id = 6)",
        );

        /**
         * Join condition for retrieves last name user.
         *
         * @var array $lastNameJoinCondition
         * */
        $lastNameJoinCondition = array(
            "({$lastNameTableAlias}.entity_id = {$tableToJoin}.customer_id)
             AND ({$lastNameTableAlias}.attribute_id = 7)",
        );

        $select->joinLeft(
            array($firstNameTableAlias => $tableCustomerVarchar),
            implode($firstNameJoinCondition),
             array('first_name' => "{$firstNameTableAlias}.value")
        )->joinLeft(
            array($middleNameTableAlias => $tableCustomerVarchar),
            implode($middleNameJoinCondition),
            array('middle_name' => "{$middleNameTableAlias}.value")
        )->joinLeft(
            array($lastNameTableAlias => $tableCustomerVarchar),
            implode($lastNameJoinCondition),
            array('last_name' => "{$lastNameTableAlias}.value")
        )->columns(array(
            'full_name' => $this->fullNameConditionMysql
        ));

        return $collection;

    }


    /**
     * Filter by user name using a like clause.
     *
     * @param $collection
     * @param $value
     */
    private function findByNameUser( $collection, $value )
    {
        $select = $collection->getSelect();
        $select->where("{$this->fullNameConditionMysql} like '%{$value}%'");

    }

}