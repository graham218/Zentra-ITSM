<?php

namespace Glpi\CalDAV\Backend;

use Sabre\DAV\Auth\Backend\AbstractBasic;

/**
 * Basic authentication backend for CalDAV server.
 *
 * @since 9.5.0
 */
class Auth extends AbstractBasic
{
    protected $principalPrefix = Principal::PREFIX_USERS . '/';

    protected function validateUserPass($username, $password)
    {
        $auth = new \Auth();
        return $auth->login($username, $password, true);
    }
}
