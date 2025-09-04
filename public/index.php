<?php

/**
 * ---------------------------------------------------------------------
 *
 * Zentra - IT Asset & Service Management System
 *
 * http://zentra-project.org
 *
 * @copyright 2025 Zentra and contributors.
 * @licence   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Zentra, a customized distribution of GLPI.
 *
 * Zentra is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zentra is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * ---------------------------------------------------------------------
 */

/**
 * Zentra web router.
 *
 * This router is used to be able to expose only the `/public` directory on the webserver.
 */

$glpi_root  = realpath(dirname(__FILE__, 2));

if (preg_match('/^\/public/', $_SERVER['REQUEST_URI']) !== 1 && $_SERVER['SCRIPT_NAME'] === '/public/index.php') {
    $uri_prefix = '';
} else {
    $uri_prefix = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
}

$path = preg_replace(
    '/^' . preg_quote($uri_prefix, '/') . '/',
    '',
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH)
);

require $glpi_root . '/src/Http/ProxyRouter.php';

$proxy = new \Glpi\Http\ProxyRouter($glpi_root, $path);

if ($proxy->isTargetAPhpScript() && $proxy->isPathAllowed() && ($target_file = $proxy->getTargetFile()) !== null) {
    chdir(dirname($target_file));
    $target_path     = $uri_prefix . $proxy->getTargetPath();
    $target_pathinfo = $proxy->getTargetPathInfo();
    $_SERVER['PATH_INFO']       = $target_pathinfo;
    $_SERVER['PHP_SELF']        = $target_path;
    $_SERVER['SCRIPT_FILENAME'] = $target_file;
    $_SERVER['SCRIPT_NAME']     = $target_path;

    require($target_file);
    exit();
}

$proxy->proxify();
