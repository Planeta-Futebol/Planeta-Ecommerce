<?php

/**
 * This Helper Class provide a some methods to support the manage custom redirects.
 *
 * @category   Manage
 * @package    Manage_Redirect
 * @author     Ronildo dos Santos - Planeta Futebol Developer Team
 */
class Manage_Redirect_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Id to representative role.
     *
     * @var int
     */
    const ID_ROLE = 101;

    /**
     * Gets current user logged in admin session, and
     * verifies if he is a representative
     *
     * @return bool
     */
    public function isRepresentative()
    {
        $role = Mage::getSingleton('admin/session')
            ->getUser()
            ->getRole()
            ->getData();

        return ($role['role_id'] == self::ID_ROLE) ? true : false;
    }
}