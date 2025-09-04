<?php

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
