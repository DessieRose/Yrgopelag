<?php

declare(strict_types=1);

// Use the ROOT_PATH we defined in autoload
$dbPath = ROOT_PATH . '/app/database/hotel.db';

return [
    'title' => 'yrgopelag',
    'database_path' => 'sqlite:' . $dbPath,
];
