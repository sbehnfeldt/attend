<?php

namespace flapjack\attend\database;

use flapjack\attend\database\Base\LoginAttempt as BaseLoginAttempt;

/**
 * Skeleton subclass for representing a row from the 'login_attempts' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class LoginAttempt extends BaseLoginAttempt
{
    public function __construct(string $username = null, $attempted_at = null, $pass = null, $note = null)
    {
        parent::__construct();
        $this->username     = $username;
        $this->attempted_at = $attempted_at;
        $this->pass         = $pass;
        $this->note         = $note;
    }
}
