<?php


namespace Glpi\CalDAV\Plugin;

use Config;
use Glpi\CalDAV\Traits\CalDAVUriUtilTrait;
use Sabre\DAV\Browser\Plugin;
use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

/**
 * Browser plugin for CalDAV server.
 *
 * @since 9.5.0
 */
class Browser extends Plugin
{
    use CalDAVUriUtilTrait;

    public function httpGet(RequestInterface $request, ResponseInterface $response)
    {
        if (!$this->canDisplayDebugInterface()) {
            return;
        }

        return parent::httpGet($request, $response);
    }

    /**
     * Check if connected user can display the HTML frontend.
     *
     * @return boolean
     */
    private function canDisplayDebugInterface()
    {
        /** @var \Sabre\DAV\Auth\Plugin $authPlugin */
        $authPlugin = $this->server->getPlugin('auth');
        if (!$authPlugin) {
            return false;
        }

        return Config::canUpdate();
    }
}
