<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'main_url' => 'http://localhost', // Main Domain URL
        'api_request_timeout' => 2, // Timeout in seconds to wait for api requests

        // MySQL settings
        'db' => [
            'host' => '127.0.0.1',
            'dbname' => 'evoucher',
            'user' => 'root',
            'password' => 'chateio'
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'evoucher',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/system.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
