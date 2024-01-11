<?php

namespace flapjack\attend\database;

use flapjack\attend\database\Base\Account as BaseAccount;


/**
 * Skeleton subclass for representing a row from the 'accounts' table.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class Account extends BaseAccount
{
    /**
     * @param  string  $permission_slug
     *
     * @return bool
     *
     * Determine whether the account holder the specified permission, as either an individual or group role
     */
    public function hasPermission(string $permission_slug): bool
    {
        return true;
    }


    public function getPermissionsList(): array
    {
        return [
            'PAGE_ATTENDANCE' => true,
            'PAGE_ENROLLMENT' => true,
            'PAGE_CLASSROOMS' => true,
            'PAGE_ADMIN'      => true,
            'PAGE_PROFILE'    => true,
        ];
    }
}
