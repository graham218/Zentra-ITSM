<?php


namespace Glpi\CalDAV;

use Glpi\Application\ErrorHandler;
use Glpi\CalDAV\Backend\Auth;
use Glpi\CalDAV\Backend\Calendar;
use Glpi\CalDAV\Backend\Principal;
use Glpi\CalDAV\Node\CalendarRoot;
use Glpi\CalDAV\Plugin\Acl;
use Glpi\CalDAV\Plugin\Browser;
use Glpi\CalDAV\Plugin\CalDAV;
use Sabre\DAV;
use Sabre\DAVACL;

class Server extends DAV\Server
{
    public function __construct()
    {
        $this->on('exception', [$this, 'logException']);

        // Backends
        $authBackend = new Auth();
        $principalBackend = new Principal();
        $calendarBackend = new Calendar();

        // Directory tree
        $tree = [
            new DAV\SimpleCollection(
                Principal::PRINCIPALS_ROOT,
                [
                    new DAVACL\PrincipalCollection($principalBackend, Principal::PREFIX_GROUPS),
                    new DAVACL\PrincipalCollection($principalBackend, Principal::PREFIX_USERS),
                ]
            ),
            new DAV\SimpleCollection(
                Calendar::CALENDAR_ROOT,
                [
                    new CalendarRoot($principalBackend, $calendarBackend, Principal::PREFIX_GROUPS),
                    new CalendarRoot($principalBackend, $calendarBackend, Principal::PREFIX_USERS),
                ]
            ),
        ];

        parent::__construct($tree);

        $this->addPlugin(new DAV\Auth\Plugin($authBackend));
        $this->addPlugin(new Acl());
        $this->addPlugin(new CalDAV());

        // Support for html frontend (only in debug mode)
        $this->addPlugin(new Browser(false));
    }

    /**
     *
     * @param \Throwable $exception
     */
    public function logException(\Throwable $exception)
    {
        if ($exception instanceof \Sabre\DAV\Exception && $exception->getHTTPCode() < 500) {
            // Ignore server exceptions that does not corresponds to a server error
            return;
        }

        ErrorHandler::getInstance()->handleException($exception, true);
    }
}
