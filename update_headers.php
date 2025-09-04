<?php
/**
 * Bulk Update Script for GLPI Headers -> Zentra
 *
 * Usage:
 *   php update_headers.php
 */

$directory = __DIR__; // project root
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

foreach ($rii as $file) {
    if ($file->isDir()) continue;
    if (pathinfo($file->getFilename(), PATHINFO_EXTENSION) !== 'php') continue;

    $content = file_get_contents($file->getPathname());

    // Replace project name
    $content = str_replace("GLPI - Gestionnaire Libre de Parc Informatique", "Zentra - IT Asset & Service Management System", $content);
    $content = str_replace("GLPI web router", "Zentra web router", $content);

    // Replace URLs
    $content = str_replace("http://glpi-project.org", "http://zentra-project.org", $content);

    // Replace copyright
    $content = preg_replace(
        "/@copyright\s+[0-9]{4}-[0-9]{4}\s+Teclib' and contributors\./",
        "@copyright 2025 Zentra and contributors.",
        $content
    );

    // Replace LICENSE section intro
    $content = str_replace("This file is part of GLPI.", "This file is part of Zentra, a customized distribution of GLPI.", $content);
    $content = str_replace("GLPI is free software", "Zentra is free software", $content);
    $content = str_replace("GLPI is distributed", "Zentra is distributed", $content);

    file_put_contents($file->getPathname(), $content);
    echo "Updated: " . $file->getPathname() . PHP_EOL;
}

echo "✅ All PHP headers updated to Zentra.\n";
