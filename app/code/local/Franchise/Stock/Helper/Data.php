<?PHP
class Franchise_Stock_Helper_Data extends Mage_Core_Helper_Abstract {

    private $searchFinancialchartSubimited = false;

    /**
     * Checks whether a search tab financialchart was submitted.
     *
     * @return boolean
     */
    public function isSearchFinancialchartSubimited()
    {
        return $this->searchFinancialchartSubimited;
    }

    /**
     * @param boolean $searchFinancialchartSubimited
     */
    public function setSearchFinancialchartSubimited( $searchFinancialchartSubimited )
    {
        $this->searchFinancialchartSubimited = $searchFinancialchartSubimited;
    }
}
