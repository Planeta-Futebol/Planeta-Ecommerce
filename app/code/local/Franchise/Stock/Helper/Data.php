<?PHP
class Franchise_Stock_Helper_Data extends Mage_Core_Helper_Abstract {

    private $commissionSubmited = false;

    /**
     * Checks whether a search tab commission was submitted.
     *
     * @return boolean
     */
    public function isCommissionSubmited()
    {
        return $this->commissionSubmited;
    }

    /**
     * @param boolean $commissionSubmited
     */
    public function setCommissionSubmited( $commissionSubmited )
    {
        $this->commissionSubmited = $commissionSubmited;
    }
}
