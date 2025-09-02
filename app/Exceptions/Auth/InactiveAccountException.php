<?php

namespace App\Exceptions\Auth;

use Exception;

class InactiveAccountException extends Exception
{
    public function __construct()
    {
        parent::__construct(__('this account is inactive'));
    }
}
