<?php

declare(strict_types=1);

return [
    'node_binary' => env('NODE_BINARY', '/home/fluxa/.nvm/versions/node/v24.14.0/bin/node'),
    'npm_binary'  => env('NPM_BINARY',  '/home/fluxa/.nvm/versions/node/v24.14.0/bin/npm'),
    'node_modules_path' => env('NODE_MODULES_PATH', '/var/www/html/node_modules'),
    'chrome_path' => env('CHROME_PATH'),
];
