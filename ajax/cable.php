<?php


use Glpi\Http\Response;
use Glpi\Socket;

/** @var array $CFG_GLPI */
global $CFG_GLPI;

include('../inc/includes.php');

// Send UTF8 Headers
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();
Session::checkCentralAccess();

$action = $_POST['action'] ?? $_GET["action"];

$itemtype = $_POST['itemtype'] ?? $_GET["itemtype"] ?? null;
$item = getItemForItemtype($itemtype);
if (
    !$item->canView()
    || (isset($_GET['items_id']) && !$item->can($_GET['items_id'], READ))
) {
    Response::sendError(403, "Not allowed");
}

switch ($action) {
    case 'get_items_from_itemtype':
        if ($_POST['itemtype'] && class_exists($_POST['itemtype'])) {
            $_POST['itemtype']::dropdown(['name'                => $_POST['dom_name'],
                'rand'                => $_POST['dom_rand'],
                'display_emptychoice' => true,
                'display_dc_position' => in_array($_POST['itemtype'], $CFG_GLPI['rackable_types']),
                'width'               => '100%',
            ]);
        }
        break;

    case 'get_socket_dropdown':
        if (
            (isset($_GET['itemtype']) && class_exists($_GET['itemtype']))
            && isset($_GET['items_id'])
        ) {
            Socket::dropdown(['name'         =>  $_GET['dom_name'],
                'condition'    => ['socketmodels_id'   => $_GET['socketmodels_id'] ?? 0,
                    'itemtype'           => $_GET['itemtype'],
                    'items_id'           => $_GET['items_id'],
                ],
                'used'         => (int) $_GET['items_id'] > 0 ? Socket::getSocketAlreadyLinked($_GET['itemtype'], (int) $_GET['items_id']) : [],
                'displaywith'  => ['itemtype', 'items_id', 'networkports_id'],
            ]);
        }
        break;

    case 'get_networkport_dropdown':
        NetworkPort::dropdown(['name'                => 'networkports_id',
            'display_emptychoice' => true,
            'condition'           => ['items_id' => $_GET['items_id'],
                'itemtype' => $_GET['itemtype'],
            ],
            'comments' => false,
        ]);
        break;


    case 'get_item_breadcrum':
        if (
            (isset($_GET['itemtype']) && class_exists($_GET['itemtype']))
            && isset($_GET['items_id']) && $_GET['items_id'] > 0
        ) {
            if (method_exists($_GET['itemtype'], 'getDcBreadcrumbSpecificValueToDisplay')) {
                echo $_GET['itemtype']::getDcBreadcrumbSpecificValueToDisplay($_GET['items_id']);
            }
        } else {
            echo "";
        }
        break;
}
